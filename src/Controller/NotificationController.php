<?php

namespace App\Controller;

use App\Entity\Dm\UsersOptions;
use App\Entity\DmStats\UtilisateursPublicationsSuggerees;
use App\Service\NotificationService;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController implements RequiresDmVersionController
{
    /**
     * @Route(
     *     methods={"POST"},
     *     path="/notification/send"
     * )
     */
    public function sendNotification(LoggerInterface $logger, NotificationService $notificationService) : Response
    {
        $notificationsSent = 0;
        try {
            $yesterday = new DateTime('yesterday midnight');

            /** @var UtilisateursPublicationsSuggerees[] $suggestedIssuesReleasedYesterday */
            $suggestedIssuesReleasedYesterday = $this->getEm('dm_stats')->getRepository(UtilisateursPublicationsSuggerees::class)
                ->findby(['oldestdate' => $yesterday]);

            $logger->info(count($suggestedIssuesReleasedYesterday).' potential notifications from suggested issues released yesterday');

            $userOptionsQb = $this->getEm('dm')->createQueryBuilder();
            $userOptionsQb
                ->from(UsersOptions::class, 'uo')
                ->innerJoin('uo.user', 'user')
                ->addSelect('user.id as user_id, user.username')
                ->addSelect('GROUP_CONCAT(uo.optionValeur) AS countries')
                ->where('uo.optionNom = :option_name')
                ->setParameter(':option_name', 'suggestion_notification_country')
                ->groupBy('user.username');

            $sql = $userOptionsQb->getQuery()->getSQL();

            $usersAndNotificationCountries = $userOptionsQb->getQuery()->getResult();

            foreach($suggestedIssuesReleasedYesterday as $suggestedIssue) {
                foreach($usersAndNotificationCountries as ['user_id' => $userId, 'username' => $username, 'countries' => $notificationCountries]) {
                    $notificationCountries = explode(',', $notificationCountries);

                    $countryCode = explode('/', $suggestedIssue->getPublicationcode())[0];
                    if (in_array(
                        $countryCode,
                        $notificationCountries,
                        true)) {
                        $notificationService->sendNotification($suggestedIssue, $userId, $username);
                        $notificationsSent++;
                    }
                    else {
                        $logger->info("User $userId doesn't want to be notified for releases of country $countryCode");
                    }
                }
            }

            $this->getEm('dm')->flush();

        } catch (Exception $e) {
            $logger->error($e->getMessage());
            return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['notifications_sent' => $notificationsSent], Response::HTTP_ACCEPTED);
    }
}
