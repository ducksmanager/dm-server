<?php

namespace DmServer\Controllers\Stats;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use DmStats\Models\AuteursHistoires;
use DmStats\Models\UtilisateursHistoiresManquantes;
use DmStats\Models\UtilisateursPublicationsManquantes;
use DmStats\Models\UtilisateursPublicationsSuggerees;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InternalController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/stats/authorsstorycount',
            function (Request $request, Application $app) {
                return AbstractController::return500ErrorOnException($app, function() {
                    $qbStoryCountPerAuthor = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM_STATS)->createQueryBuilder();
                    $qbStoryCountPerAuthor
                        ->select('author_stories.personcode, COUNT(author_stories.storycode) AS storyNumber')
                        ->from(AuteursHistoires::class, 'author_stories');

                    $storyCountResults = $qbStoryCountPerAuthor->getQuery()->getResult();

                    $storyCounts = [];
                    array_walk($storyCountResults, function($storyCount) use (&$storyCounts) {
                        $storyCounts[$storyCount['personcode']] = (int) $storyCount['storyNumber'];
                    });

                    return new JsonResponse(ModelHelper::getSerializedArray($storyCounts));
                });
            }
        );

        $routing->get(
            '/internal/stats/authorsstorycount/usercollection/missing',
            function (Request $request, Application $app) {
                return AbstractController::return500ErrorOnException($app, function() {
                    $qbMissingStoryCountPerAuthor = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM_STATS)->createQueryBuilder();
                    $qbMissingStoryCountPerAuthor
                        ->select('author_stories_missing_for_user.personcode, COUNT(author_stories_missing_for_user.storycode) AS storyNumber')
                        ->from(UtilisateursHistoiresManquantes::class, 'author_stories_missing_for_user');

                    $missingStoryCountResults = $qbMissingStoryCountPerAuthor->getQuery()->getResult();

                    $missingStoryCounts = [];
                    array_walk($missingStoryCountResults, function($storyCount) use (&$missingStoryCounts) {
                        $missingStoryCounts[$storyCount['personcode']] = (int) $storyCount['storyNumber'];
                    });

                    return new JsonResponse(ModelHelper::getSerializedArray($missingStoryCounts));
                });
            }
        );

        $routing->get(
            '/internal/stats/suggestedissues/{countrycode}',
            function (Request $request, Application $app, $countrycode) {
                return AbstractController::return500ErrorOnException($app, function() use ($app, $countrycode) {

                    $qbGetMostWantedSuggestions = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM_STATS)->createQueryBuilder();

                    $qbGetMostWantedSuggestions
                        ->select('most_suggested.publicationcode', 'most_suggested.issuenumber')
                        ->from(UtilisateursPublicationsSuggerees::class, 'most_suggested')
                        ->where($qbGetMostWantedSuggestions->expr()->in('most_suggested.idUser', ':userId'))
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

                        ->where($qbGetSuggestionDetails->expr()->in('suggested.idUser', ':userId'))
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
        )->value('countrycode', 'ALL');
    }
}
