<?php

namespace App\Controller;

use App\Entity\Dm\UsersOptions;
use App\Entity\DmStats\UtilisateursPublicationsSuggerees;
use App\Service\NotificationService;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController implements RequiresDmVersionController
{
    /**
     * @Route(methods={"POST"}, path="/notification/send/{username}/{issueCode}")
     */
    /**
     * @Route(
     *     methods={"POST"},
     *     path="/notification/send"
     * )
     */
    public function sendNotification(LoggerInterface $logger, NotificationService $notificationService) : Response
    {
        try {
            $yesterday = new DateTime('yesterday midnight');

            /** @var UtilisateursPublicationsSuggerees[] $suggestedIssuesReleasedYesterday */
            $suggestedIssuesReleasedYesterday = $this->getEm('dm_stats')->getRepository(UtilisateursPublicationsSuggerees::class)
                ->findby(['oldestdate' => $yesterday]);

            $userOptionsQb = $this->getEm('dm')->createQueryBuilder();
            $userOptionsQb
                ->from(UsersOptions::class, 'uo')
                ->innerJoin('uo.user', 'user')
                ->addSelect('user.id as user_id, user.username')
                ->addSelect('GROUP_CONCAT(uo.optionValeur) AS countries')
                ->where('uo.optionNom = :option_name')
                ->setParameter(':option_name', 'suggestion_notification_country')
                ->groupBy('user.username');

            $usersAndNotificationCountries = $userOptionsQb->getQuery()->getResult();

            foreach($suggestedIssuesReleasedYesterday as $suggestedIssue) {
                foreach($usersAndNotificationCountries as ['user_id' => $userId, 'username' => $username, 'countries' => $notificationCountries]) {
                    $notificationCountries = explode(',', $notificationCountries);

                    if (in_array(
                        explode('/', $suggestedIssue->getPublicationcode())[0],
                        $notificationCountries,
                        true)) {
                        $notificationService->sendNotification($suggestedIssue, $userId, $username);
                    }
                }
            }

            $this->getEm('dm')->flush();

        } catch (Exception $e) {
            $logger->error($e->getMessage());
            return new Response(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(Response::HTTP_ACCEPTED);
    }
}
