<?php

namespace App\Service;

use App\Entity\Dm\UsersSuggestionsNotifications;
use App\Entity\DmStats\UtilisateursPublicationsSuggerees;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use Pusher\PushNotifications\PushNotifications;

class NotificationService
{
    private static $client;
    private static $dmEm;
    private static $logger;

    public static $mockResultsStack = [];

    public function __construct(LoggerInterface $logger, ManagerRegistry $doctrineManagerRegistry)
    {
        self::$logger = $logger;
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');

        if (isset($_ENV['PUSHER_INSTANCE_ID'])) {
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

        try {
            $this->publishToUsers(
                [$username],
                [
                    'fcm' => [
                        'notification' => compact(['title', 'body'])
                    ],
                    'apns' => ['aps' => [
                        'alert' => compact(['title', 'body'])
                    ]]
                ]
            );

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
