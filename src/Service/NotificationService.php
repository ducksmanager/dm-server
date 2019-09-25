<?php

namespace App\Service;

use App\Entity\Dm\Users;
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

    public function sendNotification(UtilisateursPublicationsSuggerees $suggestedIssue, Users $user, array $notificationCountriesForUser) : bool {
        $countryCode = explode('/', $suggestedIssue->getPublicationcode())[0];
        $issueCode = "{$suggestedIssue->getPublicationcode()} {$suggestedIssue->getIssuenumber()}";

        if (!in_array(
            $countryCode,
            $notificationCountriesForUser,
            true)) {
            self::$logger->info("User {$user->getId()} doesn't want to be notified for releases of country $countryCode");
            return false;
        }

        $alreadySentNotification = self::$dmEm->getRepository(UsersSuggestionsNotifications::class)->findOneBy(['user' => $user, 'issuecode' => $issueCode]);

        if (!is_null($alreadySentNotification)) {
            self::$logger->info("A notification has already been sent to user {$user->getId()} concerning the release of issue $issueCode");
            return false;
        }
        $notificationContent = [
            'title' => self::$translator->trans('NOTIFICATION_TITLE', ['%issueTitle%' => $issueCode ]),
            'body' => self::$translator->trans('NOTIFICATION_BODY'),
        ];
        try {
            $this->publishToUsers(
                [$user->getUsername()],
                [
                    'fcm' => [
                        'notification' => $notificationContent
                    ],
                    'apns' => ['aps' => [
                        'alert' => $notificationContent
                    ]]
                ]
            );
            self::$logger->info("Notification sent to user {$user->getId()} concerning the release of issue $issueCode");

            $userSuggestionNotification = (new UsersSuggestionsNotifications())
                ->setIssuecode($issueCode)
                ->setUser($user)
                ->setNotified(true);
            self::$dmEm->persist($userSuggestionNotification);
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
}
