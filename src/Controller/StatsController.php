<?php

namespace App\Controller;

use App\Entity\Dm\Users;
use App\Entity\DmStats\AuteursHistoires;
use App\Entity\DmStats\UtilisateursHistoiresManquantes;
use App\EntityTransform\IssueSuggestion;
use App\EntityTransform\IssueSuggestionList;
use App\Service\CoaService;
use App\Service\SuggestionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController implements RequiresDmVersionController, InjectsDmUserController
{
    /**
     * @Route(methods={"GET"}, path="/collection/stats/watchedauthorsstorycount")
     */
    public function getWatchedAuthorStoryCount(CoaService $coaService) {

        $authorsAndStoryMissingForUserCount = $this->getMissingStoriesCount();

        $authorsAndStoryCount = $this->getStoriesCount(array_keys($authorsAndStoryMissingForUserCount));

        $authorsFullNames = $coaService->getAuthorNames(array_keys($authorsAndStoryCount));

        $watchedAuthorsStoryCount = [];
        array_walk($authorsFullNames, function ($authorFullName, $personCode) use (
            &$watchedAuthorsStoryCount,
            $authorsAndStoryCount,
            $authorsAndStoryMissingForUserCount
        ) {
            $watchedAuthorsStoryCount[$personCode] = [
                'fullname' => $authorFullName,
                'missingstorycount' => $authorsAndStoryMissingForUserCount[$personCode] ?? 0,
                'storycount' => $authorsAndStoryCount[$personCode] ?? 0
            ];
        });

        return new JsonResponse($watchedAuthorsStoryCount);
    }
    /**
     * @Route(
     *     methods={"GET"},
     *     path="/collection/stats/suggestedissues/{countryCode}/{sincePreviousVisit}/{sort}/{limit}",
     *     requirements={"countryCode"="^(?P<countrycode_regex>[a-z]+)|ALL|countries_to_notify", "sincePreviousVisit"="^since_previous_visit|_$"},
     *     defaults={"countryCode"="ALL", "sincePreviousVisit"="_", "sort"="score", "limit"=20}
     * )
     */
    public function getSuggestedIssuesWithDetails(?string $countryCode, string $sincePreviousVisit, string $sort, SuggestionService $suggestionService, ?int $limit) {
        $userId = $this->getSessionUser()['id'];

        switch ($countryCode) {
            case 'ALL':
                $countryCode = SuggestionService::SUGGESTION_ALL_COUNTRIES;
            break;
            case 'countries_to_notify':
                $countryCode = SuggestionService::SUGGESTION_COUNTRIES_TO_NOTIFY;
            break;
        }

        if ($sincePreviousVisit === 'since_previous_visit') {
            $user = $this->getEm('dm')->getRepository(Users::class)->find($this->getSessionUser()['id']);
            $previousVisit = $user->getPrecedentacces();
            if (!is_null($previousVisit)) {
                $since = $previousVisit;
            }
        }

        [$suggestionsPerUser, $authors, $storyDetails, $publicationTitles] = $suggestionService->getSuggestions(
            $since ?? null,
            $countryCode,
            $sort,
            $userId,
            $limit
        );
        /** @var IssueSuggestionList $suggestions */
        $suggestionsForUser = $suggestionsPerUser[$userId] ?? new IssueSuggestionList();

        return new JsonResponse([
            'minScore' => $suggestionsForUser->getMinScore(),
            'maxScore' => $suggestionsForUser->getMaxScore(),
            'issues' => (object) array_map(fn(IssueSuggestion $issue) => $issue->toSimpleObject(), $suggestionsForUser->getIssues())
        ] + compact('authors', 'storyDetails', 'publicationTitles')
        );
    }

    private function getMissingStoriesCount() : array
    {
        $qbMissingStoryCountPerAuthor = $this->getEm('dm_stats')->createQueryBuilder();
        $qbMissingStoryCountPerAuthor
            ->select('author_stories_missing_for_user.personcode, COUNT(author_stories_missing_for_user.storycode) AS storyNumber')
            ->from(UtilisateursHistoiresManquantes::class, 'author_stories_missing_for_user')
            ->where($qbMissingStoryCountPerAuthor->expr()->eq('author_stories_missing_for_user.idUser', ':userId'))
            ->setParameter(':userId', $this->getSessionUser()['id'])
            ->groupBy('author_stories_missing_for_user.personcode');

        $missingStoryCountResults = $qbMissingStoryCountPerAuthor->getQuery()->getResult();

        $missingStoryCounts = [];
        array_walk($missingStoryCountResults, function($storyCount) use (&$missingStoryCounts) {
            $missingStoryCounts[$storyCount['personcode']] = (int) $storyCount['storyNumber'];
        });

        return $missingStoryCounts;
    }

    private function getStoriesCount(array $personCodes) : array
    {
        $qbStoryCountPerAuthor = $this->getEm('dm_stats')->createQueryBuilder();
        $qbStoryCountPerAuthor
            ->select('author_stories.personcode, COUNT(author_stories.storycode) AS storyNumber')
            ->from(AuteursHistoires::class, 'author_stories')
            ->where($qbStoryCountPerAuthor->expr()->in('author_stories.personcode', ':personCodes'))
            ->setParameter('personCodes', $personCodes)
            ->groupBy('author_stories.personcode');

        $storyCountResults = $qbStoryCountPerAuthor->getQuery()->getResult();

        $storyCounts = [];
        array_walk($storyCountResults, function($storyCount) use (&$storyCounts) {
            $storyCounts[$storyCount['personcode']] = (int) $storyCount['storyNumber'];
        });

        return $storyCounts;
    }
}
