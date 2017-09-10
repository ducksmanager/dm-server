<?php

namespace DmServer\Controllers\Edges;

use Coa\Models\BaseModel;
use Dm\Models\TranchesDoublons;
use Dm\Models\TranchesPretes;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


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

        $routing->get(
            '/internal/edges/references/{publicationCode}/{issueNumbers}',
            function (Request $request, Application $app, $publicationCode, $issueNumbers) {
                return self::wrapInternalService($app, function(EntityManager $dmEm) use ($request, $publicationCode, $issueNumbers) {
                    list($country, $shortPublicationCode) = explode('/', $publicationCode);

                    $qbGetReferenceEdges = $dmEm->createQueryBuilder();
                    $qbGetReferenceEdges
                        ->select('tranches_doublons.numero as issuenumber, reference.issuenumber AS referenceissuenumber')
                        ->from(TranchesDoublons::class, 'tranches_doublons')
                        ->innerJoin('tranches_doublons.tranchereference', 'reference')
                        ->where($qbGetReferenceEdges->expr()->eq('tranches_doublons.pays', ':country'))
                        ->setParameter('country', explode(',', $country))
                        ->andWhere($qbGetReferenceEdges->expr()->in('tranches_doublons.magazine', ':shortPublicationCode'))
                        ->setParameter('shortPublicationCode', explode(',', $shortPublicationCode))
                        ->andWhere($qbGetReferenceEdges->expr()->in('tranches_doublons.numero', ':issueNumbers'))
                        ->setParameter('issueNumbers', explode(',', $issueNumbers));

                    $edgeResults = $qbGetReferenceEdges->getQuery()->getResult(Query::HYDRATE_OBJECT);
                    return new JsonResponse(ModelHelper::getSerializedArray($edgeResults));
                });
            }
        )
            ->assert('publicationCode', self::getParamAssertRegex(BaseModel::PUBLICATION_CODE_VALIDATION));
    }
}
