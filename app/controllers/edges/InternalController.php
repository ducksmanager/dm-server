<?php

namespace DmServer\Controllers\Edges;

use Coa\Models\BaseModel;
use Dm\Models\TranchesPretes;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM, $function);
    }

    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/edges/{publicationCode}/{issueNumbers}',
            function (Request $request, Application $app, $publicationCode, $issueNumbers) {
                return self::wrapInternalService($app, function(EntityManager $dmEm) use ($request, $publicationCode, $issueNumbers) {
                    $qbGetEdges = $dmEm->createQueryBuilder();
                    $qbGetEdges
                        ->select('tranches_pretes')
                        ->from(TranchesPretes::class, 'tranches_pretes')
                        ->where($qbGetEdges->expr()->eq('tranches_pretes.publicationcode', ':publicationCode'))
                        ->setParameter('publicationCode', explode(',', $publicationCode))
                        ->andWhere($qbGetEdges->expr()->in('tranches_pretes.issuenumber', ':issueNumbers'))
                        ->setParameter('issueNumbers', explode(',', $issueNumbers));

                    $edgeResults = $qbGetEdges->getQuery()->getResult();
                    return new JsonResponse(ModelHelper::getSerializedArray($edgeResults));
                });
            }
        )
            ->assert('publicationCode', self::getParamAssertRegex(BaseModel::PUBLICATION_CODE_VALIDATION));
    }
}
