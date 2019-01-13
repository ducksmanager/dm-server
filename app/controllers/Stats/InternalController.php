<?php

namespace DmServer\Controllers\Stats;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Stats\Models\AuteursHistoires;
use Stats\Models\UtilisateursHistoiresManquantes;
use Stats\Models\UtilisateursPublicationsManquantes;
use Stats\Models\UtilisateursPublicationsSuggerees;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use DDesrosiers\SilexAnnotations\Annotations as SLX;

/**
 * @SLX\Controller(prefix="/internal/stats")
 */
class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM_STATS, $function);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="authorsstorycount/{personCodes}"),
     * )
     * @param Application $app
     * @param string $personCodes
     * @return JsonResponse
     */
    public function getWatchedAuthorStoryCount(Application $app, $personCodes) {
        return self::wrapInternalService($app, function(EntityManager $statsEm) use ($personCodes) {
            $qbStoryCountPerAuthor = $statsEm->createQueryBuilder();
            $qbStoryCountPerAuthor
                ->select('author_stories.personcode, COUNT(author_stories.storycode) AS storyNumber')
                ->from(AuteursHistoires::class, 'author_stories')
                ->where($qbStoryCountPerAuthor->expr()->in('author_stories.personcode', ':personCodes'))
                ->setParameter('personCodes', explode(',', $personCodes))
                ->groupBy('author_stories.personcode');

            $storyCountResults = $qbStoryCountPerAuthor->getQuery()->getResult();

            $storyCounts = [];
            array_walk($storyCountResults, function($storyCount) use (&$storyCounts) {
                $storyCounts[$storyCount['personcode']] = (int) $storyCount['storyNumber'];
            });

            return new JsonResponse(ModelHelper::getSerializedArray($storyCounts));
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="authorsstorycount/usercollection/missing"),
     * )
     * @param Application $app
     * @return JsonResponse
     */
    public function getMissingStories(Application $app) {
        return self::wrapInternalService($app, function(EntityManager $statsEm) use($app) {
            $qbMissingStoryCountPerAuthor = $statsEm->createQueryBuilder();
            $qbMissingStoryCountPerAuthor
                ->select('author_stories_missing_for_user.personcode, COUNT(author_stories_missing_for_user.storycode) AS storyNumber')
                ->from(UtilisateursHistoiresManquantes::class, 'author_stories_missing_for_user')
                ->where($qbMissingStoryCountPerAuthor->expr()->eq('author_stories_missing_for_user.idUser', ':userId'))
                ->setParameter(':userId', self::getSessionUser($app)['id'])
                ->groupBy('author_stories_missing_for_user.personcode');

            $missingStoryCountResults = $qbMissingStoryCountPerAuthor->getQuery()->getResult();

            $missingStoryCounts = [];
            array_walk($missingStoryCountResults, function($storyCount) use (&$missingStoryCounts) {
                $missingStoryCounts[$storyCount['personcode']] = (int) $storyCount['storyNumber'];
            });

            return new JsonResponse(ModelHelper::getSerializedArray($missingStoryCounts));
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="suggestedissues/{countrycode}"),
     *     @SLX\Assert(variable="countrycode", regex="^ALL|(?P<countrycode_regex>[a-z]+)$"),
     *     @SLX\Value(variable="countrycode", default="ALL")
     * )
     * @param Application $app
     * @param string      $countrycode
     * @return JsonResponse
     */
    public function getSuggestedIssues(Application $app, $countrycode) {
        return self::wrapInternalService($app, function(EntityManager $statsEm) use ($app, $countrycode) {
            $qbGetMostWantedSuggestions = $statsEm->createQueryBuilder();

            $qbGetMostWantedSuggestions
                ->select('most_suggested.publicationcode', 'most_suggested.issuenumber')
                ->from(UtilisateursPublicationsSuggerees::class, 'most_suggested')
                ->where($qbGetMostWantedSuggestions->expr()->eq('most_suggested.idUser', ':userId'))
                ->setParameter(':userId', self::getSessionUser($app)['id'])
                ->orderBy(new OrderBy('most_suggested.score', 'DESC'))
                ->setMaxResults(20);

            if ($countrycode !== 'ALL') {
                $qbGetMostWantedSuggestions
                    ->andWhere($qbGetMostWantedSuggestions->expr()->like('most_suggested.publicationcode', ':countrycodePrefix'))
                    ->setParameter(':countrycodePrefix', $countrycode.'/%');
            }

            $mostWantedSuggestionsResults = $qbGetMostWantedSuggestions->getQuery()->getResult();

            $mostWantedSuggestions = array_map(function($suggestion) {
                return implode('', [$suggestion['publicationcode'], $suggestion['issuenumber']]);
            }, $mostWantedSuggestionsResults);

            $qbGetSuggestionDetails = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM_STATS)->createQueryBuilder();

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
                ->setParameter(':userId', self::getSessionUser($app)['id'])

                ->andWhere($qbGetSuggestionDetails->expr()->in($qbGetSuggestionDetails->expr()->concat('suggested.publicationcode', 'suggested.issuenumber'), ':mostSuggestedIssues'))
                ->setParameter(':mostSuggestedIssues', $mostWantedSuggestions)

                ->addOrderBy(new OrderBy('suggested.score', 'DESC'))
                ->addOrderBy(new OrderBy('suggested.publicationcode', 'ASC'))
                ->addOrderBy(new OrderBy('suggested.issuenumber', 'ASC'));

            $suggestionResults = $qbGetSuggestionDetails->getQuery()->getResult();

            return new JsonResponse(ModelHelper::getSerializedArray($suggestionResults));
        });
    }
}
