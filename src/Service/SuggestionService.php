<?php
namespace App\Service;

use App\Entity\DmStats\UtilisateursPublicationsSuggerees;
use App\EntityTransform\IssueSuggestion;
use App\EntityTransform\IssueSuggestionList;
use App\EntityTransform\UserWithOptionValue;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Exception;

class SuggestionService
{
    public const SUGGESTION_ALL_COUNTRIES = 'ALL';
    public const SUGGESTION_COUNTRIES_TO_NOTIFY = 'countries_to_notify';

    /** @var EntityManager */
    private static $dmEm;

    /** @var EntityManager */
    private static $dmStatsEm;

    /** @var CoaService */
    private static CoaService $coaService;

    /** @var UsersOptionsService */
    private static UsersOptionsService $usersOptionsService;

    public function __construct(ManagerRegistry $doctrineManagerRegistry, CoaService $coaService, UsersOptionsService $usersOptionsService)
    {
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');
        self::$dmStatsEm = $doctrineManagerRegistry->getManager('dm_stats');
        self::$coaService = $coaService;
        self::$usersOptionsService = $usersOptionsService;
    }

    /**
     * @param DateTime|null $since
     * @param string $countryCode
     * @param string $sort
     * @param int|null $singleUserId
     * @param int|null $limit
     * @return UtilisateursPublicationsSuggerees[]
     * @throws Exception
     */
    public function getSuggestions(?DateTime $since, string $countryCode, string $sort = 'Score', ?int $singleUserId = null, ?int $limit = null) : array
    {
        if (!in_array($sort, ['score', 'oldestdate'])) {
            return [[], [], [], []];
        }
        $singleCountry = in_array($countryCode, [self::SUGGESTION_ALL_COUNTRIES, self::SUGGESTION_COUNTRIES_TO_NOTIFY], true) ? null : $countryCode;

        $dateFilter = isset($since) ? ' AND suggested.oldestdate > :sinceDate ' : '';
        $userFilter = isset($singleUserId) ? ' AND suggested.ID_User = :userId ' : '';
        $countryFilter = isset($singleCountry) ? ' AND suggested.publicationcode LIKE :publicationCode ' : '';
        $sql = "
            SELECT
                suggested.ID_User as user_id,
                suggested.Score as score,
                suggested.publicationcode,
                suggested.issuenumber,
                suggested.oldestdate,
                missing.personcode,
                missing.storycode
            FROM utilisateurs_publications_suggerees as suggested
            INNER JOIN utilisateurs_publications_manquantes as missing
                USING (ID_User, publicationcode, issuenumber)
            WHERE suggested.oldestdate <= :untilDate
                  $dateFilter
                  $userFilter
                  $countryFilter
            ORDER BY ID_User, $sort DESC, publicationcode, issuenumber";

        $suggestions = self::$dmStatsEm->getConnection()->fetchAllAssociative($sql,
           [ ':untilDate' => (new DateTime())->format('Y-m-d')] +
           (empty($dateFilter) ? [] : [ ':sinceDate' => $since->format('Y-m-d')]) +
           (empty($userFilter) ? [] : [ ':userId' => $singleUserId]) +
           (empty($countryFilter) ? [] : [ ':publicationCode' => "$singleCountry/%"])
        );

        if (empty($suggestions)) {
            return [[], [], [], []];
        }

        if ($countryCode === self::SUGGESTION_COUNTRIES_TO_NOTIFY) {
            $countriesToNotifyPerUser = self::$usersOptionsService->getOptionValueAllUsers(UsersOptionsService::OPTION_NAME_SUGGESTION_NOTIFICATION_COUNTRY);
        }

        /** @var IssueSuggestionList[] $suggestionsPerUser */
        $suggestionsPerUser = [];
        $referencedIssues = [];
        $referencedStories = [];
        foreach($suggestions as $suggestedStory) {
            $userId = $suggestedStory['user_id'];
            if (self::isSuggestionInCountriesToNotify($countriesToNotifyPerUser ?? null, $userId, $suggestedStory)) {
                if (!array_key_exists($userId, $suggestionsPerUser)) {
                    $suggestionsPerUser[$userId] = new IssueSuggestionList();
                }

                $issueCode = implode(' ', [$suggestedStory['publicationcode'], $suggestedStory['issuenumber']]);

                if (!is_null($limit) && !array_key_exists($issueCode, $suggestionsPerUser[$userId]->getIssues()) && $suggestionsPerUser[$userId]->getIssuesCount() >= $limit) {
                    continue;
                }

                $issue = $suggestionsPerUser[$userId]->getIssueWithCode($issueCode);
                if (!isset($issue)) {
                    $issue = new IssueSuggestion(
                        $issueCode,
                        $suggestedStory['score'],
                        [],
                        $suggestedStory['publicationcode'],
                        $suggestedStory['issuenumber'],
                        $suggestedStory['oldestdate']
                    );
                }

                $issue->addStoryCodeForAuthor($suggestedStory['personcode'], $suggestedStory['storycode']);
                $suggestionsPerUser[$userId]->addOrReplaceIssue($issue);
                $referencedIssues[]=$issue;
                $referencedStories[]=$suggestedStory;
            }
        }

        $authors = self::$coaService->getAuthorNames(array_map(function ($story) {
            return $story['personcode'];
        }, $referencedStories));

        $storyDetails = self::$coaService->getStoryDetails(
            array_map(fn($story) => $story['storycode'], $referencedStories),
            array_map(fn($story) => $story['publicationcode'], $referencedStories),
            array_map(fn($story) => $story['issuenumber'], $referencedStories)
        );

        // Add author to story details
        foreach ($referencedStories as $referencedStory) {
            $storyDetails[$referencedStory['storycode']]['personcode'] = $referencedStory['personcode'];
        }

        $publicationTitles = self::$coaService->getPublicationTitles(array_map(function (IssueSuggestion $issueSuggestion) {
            return $issueSuggestion->getPublicationcode();
        }, $referencedIssues));

        return [$suggestionsPerUser, $authors, $storyDetails, $publicationTitles];
    }

    /**
     * @param UserWithOptionValue[]|null $countriesToNotify
     * @param int $userId
     * @param array $suggestion
     * @return bool
     */
    private static function isSuggestionInCountriesToNotify(?array $countriesToNotify, int $userId, array $suggestion) : bool {
        if (is_null($countriesToNotify)) {
            return true;
        }
        if (!isset($countriesToNotify[$userId])) {
            return false;
        }
        $countriesToNotifyUser = $countriesToNotify[$userId]->getValue();
        foreach($countriesToNotifyUser as $countryToNotify) {
            if (strpos($suggestion['publicationcode'], "$countryToNotify/") === 0) {
                return true;
            }
        }
        return false;
    }
}
