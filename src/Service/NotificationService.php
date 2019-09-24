<?php

namespace App\Service;

use App\Entity\Dm\UsersSuggestionsNotifications;
use App\Entity\DmStats\UtilisateursPublicationsSuggerees;
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

    public function sendNotification(UtilisateursPublicationsSuggerees $suggestedIssue, int $userId, string $username) {
        $issueCode = "{$suggestedIssue->getPublicationcode()} {$suggestedIssue->getIssuenumber()}";

        $notificationContent = [
            'title' => self::$translator->trans('NOTIFICATION_TITLE', ['%issueTitle%' => $issueCode ]),
            'body' => self::$translator->trans('NOTIFICATION_BODY'),
        ];
        try {
            $this->publishToUsers(
                [$username],
                [
                    'fcm' => [
                        'notification' => $notificationContent
                    ],
                    'apns' => ['aps' => [
                        'alert' => $notificationContent
                    ]]
                ]
            );
            self::$logger->info("Notification sent to user $userId concerning the release of issue $issueCode");

            $userSuggestionNotification = (new UsersSuggestionsNotifications())
                ->setIssuecode($issueCode)
                ->setUserId($userId)
                ->setNotified(true);
            self::$dmEm->persist($userSuggestionNotification);

        } catch (Exception $e) {
            self::$logger->error($e->getMessage());
        }
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
