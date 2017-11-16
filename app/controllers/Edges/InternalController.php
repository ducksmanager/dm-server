<?php

namespace DmServer\Controllers\Edges;

use Dm\Models\TranchesDoublons;
use Dm\Models\TranchesPretes;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use DDesrosiers\SilexAnnotations\Annotations as SLX;

/**
 * @SLX\Controller(prefix="/internal/edges")
 */
class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM, $function);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="{publicationCode}/{issueNumbers}"),
     *     @SLX\Assert(variable="publicationCode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *     @SLX\Assert(variable="issueNumbers", regex="^((?P<issuenumber_regex>[-A-Z0-9 ]+),){0,49}(?&issuenumber_regex)$")
     * )
     * @param Application $app
     * @param string $publicationCode
     * @param $issueNumbers
     * @return JsonResponse
     */
    public function getEdges(Application $app, $publicationCode, $issueNumbers) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($publicationCode, $issueNumbers) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="references/{publicationCode}/{issueNumbers}"),
     *     @SLX\Assert(variable="publicationCode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *     @SLX\Assert(variable="issueNumbers", regex="^((?P<issuenumber_regex>[-A-Z0-9 ]+),){0,49}(?&issuenumber_regex)$")
     * )
     * @param Application $app
     * @param string $publicationCode
     * @param $issueNumbers
     * @return JsonResponse
     */
    public function getEdgeReferences(Application $app, $publicationCode, $issueNumbers) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($publicationCode, $issueNumbers) {
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
}
