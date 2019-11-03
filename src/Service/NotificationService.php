<?php

namespace App\Service;

use App\Entity\Dm\Users;
use App\Entity\Dm\UsersSuggestionsNotifications;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use Pusher\PushNotifications\PushNotifications;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationService
{
    private static $client;
    private static $dmEm;
    private static $logger;
    private static $translator;

    public static $mockResultsStack = [];

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
     * @param string $suggestedIssueCode
     * @param Users[] $usersToNotify
     * @return int
     */
    public function sendSuggestedIssueNotification(string $suggestedIssueCode, array $usersToNotify) : int {

        $notificationContent = [
            'title' => self::$translator->trans('NOTIFICATION_TITLE', ['%issueTitle%' => $suggestedIssueCode ]),
            'body' => self::$translator->trans('NOTIFICATION_BODY'),
        ];
        try {
            $this->publishToUsers(
                array_map(function(Users $user) { return $user->getUsername(); }, $usersToNotify),
                [
                    'fcm' => [
                        'notification' => $notificationContent
                    ],
                    'apns' => ['aps' => [
                        'alert' => $notificationContent
                    ]]
                ]
            );
            foreach($usersToNotify as $userNotified) {
                self::$logger->info("Notification sent to user {$userNotified->getId()} concerning the release of issue $suggestedIssueCode");
                $userSuggestionNotification = (new UsersSuggestionsNotifications())
                    ->setIssuecode($suggestedIssueCode)
                    ->setUser($userNotified)
                    ->setNotified(true);
                self::$dmEm->persist($userSuggestionNotification);
            }

            return count($usersToNotify);

        } catch (Exception $e) {
            self::$logger->error($e->getMessage());
        }

        return 0;
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
}
