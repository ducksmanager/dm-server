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
                ->select('uo AS user_options')
                ->from(UsersOptions::class, 'uo')
                ->addSelect('GROUP_CONCAT(uo.optionValeur) AS countries')
                ->innerJoin('uo.user', 'u')
                ->where('uo.optionNom = :option_name')
                ->setParameter(':option_name', 'suggestion_notification_country')
                ->groupBy('u.id');

            $dql = $userOptionsQb->getQuery()->getDQL();
            $usersAndNotificationCountries = $userOptionsQb->getQuery()->getResult();

            foreach($suggestedIssuesReleasedYesterday as $suggestedIssue) {
                /** @var UsersOptions $userOptions */
                /** @var string $notificationCountries */
                foreach($usersAndNotificationCountries as ['user_options' => $userOptions, 'countries' => $notificationCountries]) {
                    $notificationCountriesForUser = explode(',', $notificationCountries);

                    if ($notificationService->sendNotification($suggestedIssue, $userOptions->getUser(), $notificationCountriesForUser)) {
                        $notificationsSent++;
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
