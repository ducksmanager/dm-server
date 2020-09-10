<?php

namespace App\Controller;

use App\Entity\Dm\Users;
use App\Service\NotificationService;
use App\Service\SuggestionService;
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
    public function sendNotification(LoggerInterface $logger, SuggestionService $suggestionService, NotificationService $notificationService) : Response
    {
        $suggestionsSince = new DateTime('-7 days midnight');
        $notificationsSent = 0;
        try {
            [$suggestionsPerUser, $authors, , $publicationTitles] = $suggestionService->getSuggestions(
                $suggestionsSince,
                SuggestionService::SUGGESTION_COUNTRIES_TO_NOTIFY
            );

            $qb = $this->getEm('dm')->createQueryBuilder();
            $qb->select('u')->from(Users::class, 'u', 'u.id');
            $users = $qb->getQuery()->getResult();

            foreach($suggestionsPerUser as $userId => $suggestionsForUser) {
                $suggestedIssuesForUserNotAlreadySent = $notificationService->filterUnNotifiedIssues(
                    $suggestionsForUser,
                    $userId,
                );

                $logger->info(count($suggestedIssuesForUserNotAlreadySent)." new issues will be suggested to user $userId");
                $logger->info((count($suggestionsForUser->getIssues()) - count($suggestedIssuesForUserNotAlreadySent))." issues have already been suggested to user $userId");

                foreach($suggestedIssuesForUserNotAlreadySent as $suggestedIssue) {
                    $issueTitle = $publicationTitles[$suggestedIssue->getPublicationcode()].' '.$suggestedIssue->getIssuenumber();
                    $storyCountPerPersonCode = array_map(function(array $storycodes) : int {
                        return count($storycodes);
                    }, $suggestedIssue->getStories());
                    $storyCountPerAuthor = [];
                    foreach($storyCountPerPersonCode as $personcode => $storyCount) {
                        $storyCountPerAuthor[$authors[$personcode]] = $storyCount;
                    }
                    $notificationsSent += $notificationService->sendSuggestedIssueNotification($suggestedIssue->getIssuecode(), $issueTitle, $storyCountPerAuthor, $users[$userId]);
                }
            }
        } catch (Exception $e) {
            $logger->error($e->getMessage());
            return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['notifications_sent' => $notificationsSent], Response::HTTP_ACCEPTED);
    }
}
