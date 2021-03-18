<?php

namespace App\Service;

use App\Entity\Dm\Users;
use App\Entity\Dm\UsersSuggestionsNotifications;
use App\EntityTransform\IssueSuggestion;
use App\EntityTransform\IssueSuggestionList;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Exception;
use Psr\Log\LoggerInterface;
use Pusher\PushNotifications\PushNotifications;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationService
{
    private static PushNotifications $client;
    /** @var EntityManager */
    private static $dmEm;
    private static LoggerInterface $logger;
    private static TranslatorInterface $translator;

    public static array $mockResultsStack = [];

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger, ManagerRegistry $doctrineManagerRegistry)
    {
        self::$logger = $logger;
        self::$translator = $translator;
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');

        if (!empty($_ENV['PUSHER_INSTANCE_ID'])) {
            try {
                self::$client = new PushNotifications([
                    'instanceId' => $_ENV['PUSHER_INSTANCE_ID'],
                    'secretKey' => $_ENV['PUSHER_SECRET_KEY'],
                ]);
            } catch (Exception $e) {
                self::$logger->error($e->getMessage());
            }
        }
    }

    /**
     * @param string $issueCode
     * @param string $issueTitle
     * @param int[] $storyCountPerAuthor
     * @param Users $userToNotify
     * @return bool
     */
    public function sendSuggestedIssueNotification(string $issueCode, string $issueTitle, array $storyCountPerAuthor, Users $userToNotify) : bool {

        $notificationContent = [
            'title' => self::$translator->trans('NOTIFICATION_TITLE', ['%issueTitle%' => $issueTitle ]),
            'body' => implode('', array_map(function(string $authorName) use ($storyCountPerAuthor) {
                $storyCount = $storyCountPerAuthor[$authorName];
                return self::$translator->trans($storyCount === 1 ? 'NOTIFICATION_BODY_ONE_STORY' : 'NOTIFICATION_BODY_MULTIPLE_STORIES', ['%author%' => $authorName]);
            }, array_keys($storyCountPerAuthor)))
        ];
        try {
            $this->publishToUsers(
                [$userToNotify->getUsername()],
                [
                    'fcm' => [
                        'notification' => $notificationContent
                    ],
                    'apns' => ['aps' => [
                        'alert' => $notificationContent
                    ]]
                ]
            );
            self::$logger->info("Notification sent to user {$userToNotify->getId()} concerning the release of issue $issueTitle");
            $userSuggestionNotification = (new UsersSuggestionsNotifications())
                ->setIssuecode($issueCode)
                ->setText($issueTitle)
                ->setUser($userToNotify)
                ->setDate(new DateTime());
            self::$dmEm->persist($userSuggestionNotification);
            self::$dmEm->flush();

            return true;
        } catch (Exception $e) {
            self::$logger->error($e->getMessage());
        }

        return false;
    }

    /**
     * @param $userIds
     * @param $publishRequest
     * @return mixed
     * @throws Exception
     */
    private function publishToUsers($userIds, $publishRequest) {
        if (isset(self::$client)) {
            return self::$client->publishToUsers($userIds, $publishRequest);
        }

        return array_pop(self::$mockResultsStack);
    }

    /**
     * @param IssueSuggestionList $suggestedIssuesToNotify
     * @param int $userId
     * @return IssueSuggestion[]
     */
    public function filterUnNotifiedIssues(IssueSuggestionList $suggestedIssuesToNotify, int $userId): array
    {
        return array_filter($suggestedIssuesToNotify->getIssues(), function(IssueSuggestion $suggestedIssue) use ($userId) {
            $suggestedIssueCode = "{$suggestedIssue->getPublicationcode()} {$suggestedIssue->getIssuenumber()}";

            $alreadySentNotificationQb = self::$dmEm->createQueryBuilder();
            $alreadySentNotificationQb
                ->select('existingNotification')
                ->from(UsersSuggestionsNotifications::class, 'existingNotification')
                ->innerJoin('existingNotification.user', 'u')
                ->where('u.id = :userId')
                ->setParameter(':userId', $userId)
                ->andWhere('existingNotification.issuecode = :issueCode')
                ->setParameter(':issueCode', $suggestedIssueCode);

            return empty($alreadySentNotificationQb->getQuery()->getResult());
        });
    }
}
