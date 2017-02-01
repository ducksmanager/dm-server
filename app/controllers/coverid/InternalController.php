<?php

namespace DmServer\Controllers\Coverid;

use CoverId\Models\Covers;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
            '/internal/cover-id/issuecodes/{coverids}',
            function (Request $request, Application $app, $coverids) {
                return AbstractController::return500ErrorOnException($app, function() use ($coverids) {
                    $coveridsList = explode(',', $coverids);

                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COVER_ID)->createQueryBuilder();
                    $qb
                        ->select('covers.issuecode')
                        ->from(Covers::class, 'covers');

                    $qb->where($qb->expr()->in('covers.id', $coveridsList));

                    $results = $qb->getQuery()->getResult();

                    array_walk(
                        $results,
                        function($issue, $i) use ($coveridsList, &$issueCodes) {
                            $issueCodes[$coveridsList[$i]] = $issue['issuecode'];
                        }
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issueCodes));
                });
            }
        )->assert('coverids', '^([0-9]+,)*[0-9]+$');

        $routing->get(
            '/internal/cover-id/download/{coverUrl}',
            function (Request $request, Application $app, $coverUrl) {
                return AbstractController::return500ErrorOnException($app, function() use ($coverUrl) {
                    $localFilePath = DmServer::$settings['image_local_root'] . basename($coverUrl);

                    @mkdir(DmServer::$settings['image_local_root'].dirname($coverUrl), 0777, true);
                    file_put_contents(
                        $localFilePath,
                        file_get_contents(DmServer::$settings['image_remote_root'] . $coverUrl)
                    );

                    return new BinaryFileResponse($localFilePath);
                });
            }
        )->assert('coverUrl', '.+');
    }
}
