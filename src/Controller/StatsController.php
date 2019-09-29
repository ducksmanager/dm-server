<?php

namespace App\Controller;

use App\Entity\Dm\Users;
use App\Entity\Dm\UsersOptions;
use App\Entity\DmStats\AuteursHistoires;
use App\Entity\DmStats\UtilisateursHistoiresManquantes;
use App\Entity\DmStats\UtilisateursPublicationsManquantes;
use App\Entity\DmStats\UtilisateursPublicationsSuggerees;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController implements RequiresDmVersionController, RequiresDmUserController
{
    /**
     * @Route(methods={"GET"}, path="/collection/stats/watchedauthorsstorycount")
     */
    public function getWatchedAuthorStoryCount() {

        $authorsAndStoryMissingForUserCount = $this->getMissingStoriesCount();

        $authorsAndStoryCount = $this->getStoriesCount(array_keys($authorsAndStoryMissingForUserCount));

        $authorsFullNames = json_decode(
            $this->callService(CoaController::class, 'listAuthorsFromAuthorCodes', [
                'authors' => implode(',', array_keys($authorsAndStoryCount))
            ])->getContent()
        );

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
     *     path="/collection/stats/suggestedissues/{countryCode}/{sincePreviousVisit}",
     *     requirements={"countryCode"="^(?P<countrycode_regex>[a-z]+)|ALL$", "sincePreviousVisit"="^since_previous_visit|_$"},
     *     defaults={"countryCode"="ALL", "sincePreviousVisit"="_"}
     * )
     */
    public function getSuggestedIssuesWithDetails(string $countryCode, string $sincePreviousVisit) {
        $countryCodes = $countryCode === 'ALL' ? null : [$countryCode];

        if ($sincePreviousVisit === 'since_previous_visit') {
            /** @var Users $user */
            $user = $this->getEm('dm')->getRepository(Users::class)->find($this->getCurrentUser()['id']);
            if (is_null($countryCodes)) {
                $userCountryNotifications = $this->getEm('dm')->getRepository(UsersOptions::class)->findBy([
                    'user' => $user,
                    'optionNom' => 'suggestion_notification_country'
                ]);
                $countryCodes = array_map(function(UsersOptions $option) {
                    return $option->getOptionValeur();
                }, $userCountryNotifications);
            }
            $previousVisit = $user->getPrecedentacces();
            if (!is_null($previousVisit)) {
                $since = $previousVisit->format('Y-m-d');
            }
        }
        $suggestedStories = $this->getSuggestedIssues($countryCodes, $since ?? 'forever');

        if (count($suggestedStories) === 0) {
            return new JsonResponse([
                'maxScore' => 0,
                'minScore' => 0,
                'issues' => new stdClass(),
                'authors' => new stdClass(),
                'publicationTitles' => new stdClass(),
                'storyDetails' => new stdClass()
            ]);
        }

        // Get author names
        $storyAuthors = array_map(function ($story) {
            return $story['personcode'];
        }, $suggestedStories);

        $authors = json_decode(
            $this->callService(CoaController::class, 'listAuthorsFromAuthorCodes', [
                'authors' => implode(',', $storyAuthors)
            ])->getContent()
        );

        // Get author names - END

        // Get story details
        $storyCodes = array_map(function ($story) {
            return $story['storycode'];
        }, $suggestedStories);

        $storyDetails = json_decode(
            $this->callService(CoaController::class, 'listStoryDetailsFromStoryCodes', [
                'storyCodes' => implode(',', $storyCodes)
            ])->getContent()
        );

        // Add author to story details
        foreach ($suggestedStories as $suggestedStory) {
            $storyDetails->{$suggestedStory['storycode']}->personcode = $suggestedStory['personcode'];
        }

        // Get story details - END

        // Get publication titles
        $publicationCodes = array_map(function ($story) {
            return $story['publicationcode'];
        }, $suggestedStories);

        $publicationTitles = json_decode(
            $this->callService(CoaController::class, 'listPublicationsFromPublicationCodes', [
                'publicationCodes' => implode(',', $publicationCodes)
            ])->getContent()
        );

        // Get publication titles - END

        $issues = [];
        foreach ($suggestedStories as $story) {
            ['publicationcode' => $publicationcode, 'issuenumber' => $issuenumber, 'personcode' => $personcode, 'score' => $score, 'storycode' => $storycode] = $story;
            $issueCode = implode(' ', [$publicationcode, $issuenumber]);
            if (!isset($issues[$issueCode]['stories'])) {
                $issues[$issueCode] =
                    ['stories' => []]
                    + compact('score', 'publicationcode', 'issuenumber');
            }
            if (!isset($issues[$issueCode]['stories'][$personcode])) {
                $issues[$issueCode]['stories'][$personcode] = [];
            }
            $issues[$issueCode]['stories'][$personcode][] = $storycode;
        }

        return new JsonResponse([
            'maxScore' => $suggestedStories[0]['score'],
            'minScore' => $suggestedStories[count($suggestedStories) - 1]['score'],
            'issues' => json_decode(json_encode($issues)),
            'authors' => $authors,
            'publicationTitles' => $publicationTitles,
            'storyDetails' => $storyDetails
        ]);
    }

    private function getMissingStoriesCount() : array
    {
        $qbMissingStoryCountPerAuthor = $this->getEm('dm_stats')->createQueryBuilder();
        $qbMissingStoryCountPerAuthor
            ->select('author_stories_missing_for_user.personcode, COUNT(author_stories_missing_for_user.storycode) AS storyNumber')
            ->from(UtilisateursHistoiresManquantes::class, 'author_stories_missing_for_user')
            ->where($qbMissingStoryCountPerAuthor->expr()->eq('author_stories_missing_for_user.idUser', ':userId'))
            ->setParameter(':userId', $this->getCurrentUser()['id'])
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

    private function getSuggestedIssues(?array $countryCodes, string $since) : array
    {
        $qbGetMostWantedSuggestions = $this->getEm('dm_stats')->createQueryBuilder();

        $qbGetMostWantedSuggestions
            ->select('most_suggested.publicationcode', 'most_suggested.issuenumber')
            ->from(UtilisateursPublicationsSuggerees::class, 'most_suggested')
            ->where($qbGetMostWantedSuggestions->expr()->eq('most_suggested.idUser', ':userId'))
            ->setParameter(':userId', $this->getCurrentUser()['id'])
            ->orderBy(new OrderBy('most_suggested.score', 'DESC'))
            ->setMaxResults(20);

        if (!is_null($countryCodes)) {
            $qbGetMostWantedSuggestions
                ->andWhere('('.implode(' or ', array_map(function(string $countryCode) {
                    return "most_suggested.publicationcode LIKE :countryCodeParameter$countryCode";
                }, $countryCodes)).')');
            foreach($countryCodes as $countryCode) {
                $qbGetMostWantedSuggestions->setParameter(":countryCodeParameter$countryCode", "$countryCode/%");
            }
        }

        if ($since !== 'forever') {
            $qbGetMostWantedSuggestions
                ->andWhere($qbGetMostWantedSuggestions->expr()->gt('most_suggested.oldestdate', ':since'))
                ->setParameter(':since', $since);
        }

        $mostWantedSuggestionsResults = $qbGetMostWantedSuggestions->getQuery()->getResult();

        $mostWantedSuggestions = array_map(function($suggestion) {
            return implode('', [$suggestion['publicationcode'], $suggestion['issuenumber']]);
        }, $mostWantedSuggestionsResults);

        $qbGetSuggestionDetails = $this->getEm('dm_stats')->createQueryBuilder();

        $qbGetSuggestionDetails
            ->select('missing.personcode, missing.storycode, ' .
                'suggested.publicationcode, suggested.issuenumber, suggested.score')
            ->from(UtilisateursPublicationsSuggerees::class, 'suggested')
            ->join(UtilisateursPublicationsManquantes::class, 'missing', Join::WITH,  $qbGetSuggestionDetails->expr()->andX(
                $qbGetSuggestionDetails->expr()->eq('suggested.idUser', 'missing.idUser'),
                $qbGetSuggestionDetails->expr()->eq('suggested.publicationcode', 'missing.publicationcode'),
                $qbGetSuggestionDetails->expr()->eq('suggested.issuenumber', 'missing.issuenumber')
            ))

            ->where($qbGetSuggestionDetails->expr()->eq('suggested.idUser', ':userId'))
            ->setParameter(':userId', $this->getCurrentUser()['id'])

            ->andWhere($qbGetSuggestionDetails->expr()->in($qbGetSuggestionDetails->expr()->concat('suggested.publicationcode', 'suggested.issuenumber'), ':mostSuggestedIssues'))
            ->setParameter(':mostSuggestedIssues', $mostWantedSuggestions)

            ->addOrderBy(new OrderBy('suggested.score', 'DESC'))
            ->addOrderBy(new OrderBy('suggested.publicationcode', 'ASC'))
            ->addOrderBy(new OrderBy('suggested.issuenumber', 'ASC'));

        return $qbGetSuggestionDetails->getQuery()->getResult();
    }
}
