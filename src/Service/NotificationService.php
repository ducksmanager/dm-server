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
     * @param string $issueCode
     * @param string $text
     * @param Users[] $usersToNotify
     * @return int
     */
    public function sendSuggestedIssueNotification(string $issueCode, string $text, array $usersToNotify) : int {

        $notificationContent = [
            'title' => self::$translator->trans('NOTIFICATION_TITLE', ['%issueTitle%' => $text ]),
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
                $userSuggestionNotification = (new UsersSuggestionsNotifications())
                    ->setIssuecode($issueCode)
                    ->setText($text)
                    ->setUser($userNotified)
                    ->setNotified(true);
                self::$dmEm->persist($userSuggestionNotification);
                self::$logger->info("Notification sent to user {$userNotified->getId()} concerning the release of issue $text");
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
