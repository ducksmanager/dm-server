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
            '/internal/stats/suggestedpublications',
            function (Request $request, Application $app) {
                return AbstractController::return500ErrorOnException($app, function() use ($app) {
                    $qbGetSuggestions = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM_STATS)->createQueryBuilder();

                    $qbGetSuggestions
                        ->select('missing_publications.personcode, missing_publications.storycode, ' .
                                 'suggested_publications.publicationcode, suggested_publications.issuenumber, suggested_publications.score')
                        ->from(UtilisateursPublicationsSuggerees::class, 'suggested_publications')
                        ->join(UtilisateursPublicationsManquantes::class, 'missing_publications', Join::WITH, $qbGetSuggestions->expr()->andX(
                            $qbGetSuggestions->expr()->eq('suggested_publications.idUser', 'missing_publications.idUser'),
                            $qbGetSuggestions->expr()->eq('suggested_publications.publicationcode', 'missing_publications.publicationcode'),
                            $qbGetSuggestions->expr()->eq('suggested_publications.issuenumber', 'missing_publications.issuenumber')
                        ))

                        ->where($qbGetSuggestions->expr()->in('suggested_publications.idUser', ':userId'))
                        ->setParameter(':userId', self::getSessionUser($app)['id'])
                        ->orderBy(new OrderBy('suggested_publications.score', 'DESC'))
                        ->setMaxResults(20)
                        ;
                    $sql = $qbGetSuggestions->getQuery()->getSQL();
                    $suggestionResults = $qbGetSuggestions->getQuery()->getResult();

                    return new JsonResponse(ModelHelper::getSerializedArray($suggestionResults));
                });
            }
        );
    }
}
