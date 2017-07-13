<?php

namespace DmServer\Controllers\Coverid;

use CoverId\Models\Covers;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Func;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::return500ErrorOnException($app, DmServer::CONFIG_DB_KEY_COVER_ID, $function);
    }
    
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/cover-id/issuecodes/{coverids}',
            function (Request $request, Application $app, $coverids) {
                return self::wrapInternalService($app, function(EntityManager $coverEm) use ($coverids) {
                    $coveridsList = explode(',', $coverids);

                    $qb = $coverEm->createQueryBuilder();
                    $qb
                        ->select('covers.issuecode, covers.url')
                        ->from(Covers::class, 'covers');

                    $qb->where($qb->expr()->in('covers.id', $coveridsList));

                    $results = $qb->getQuery()->getResult();

                    array_walk(
                        $results,
                        function ($cover, $i) use ($coveridsList, &$coverInfos) {
                            $coverInfos[$coveridsList[$i]] = ['url' => $cover['url'], 'issuecode' => $cover['issuecode']];
                        }
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($coverInfos));
                });
            }
        )->assert('coverids', '^([0-9]+,)*[0-9]+$');

        $routing->get(
            '/internal/cover-id/download/{coverId}',
            function (Request $request, Application $app, $coverId) {
                return self::wrapInternalService($app, function(EntityManager $coverEm) use ($coverId) {
                    $qb = $coverEm->createQueryBuilder();

                    $concatFunc = new Func('CONCAT', [
                        $qb->expr()->literal('https://outducks.org/'),
                        'covers.sitecode',
                        $qb->expr()->literal('/'),
                        'case covers.sitecode when \'webusers\' then \'webusers/\' else \'\' end',
                        'covers.url'
                    ]);

                    $qb
                        ->select(
                            'covers.url',
                            $concatFunc. 'as full_url')
                        ->from(Covers::class, 'covers')
                        ->where($qb->expr()->eq('covers.id', $coverId));

                    $result = $qb->getQuery()->getOneOrNullResult();
                    $url = $result['url'];
                    $fullUrl = $result['full_url'];

                    $localFilePath = DmServer::$settings['image_local_root'] . basename($url);
                    @mkdir(DmServer::$settings['image_local_root'] . dirname($url), 0777, true);
                    file_put_contents(
                        $localFilePath,
                        file_get_contents(
                            $fullUrl,
                            false,
                            stream_context_create([
                                "ssl" => [
                                    'verify_peer' => false,
                                    'verify_peer_name' => false
                                ]
                            ])
                        )
                    );

                    return new BinaryFileResponse($localFilePath);
                });
            }
        )->assert('coverId', '[0-9]+');
    }
}
