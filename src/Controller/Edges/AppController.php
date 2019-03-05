<?php

namespace App\Controller\Edges;

use App\Controller\AbstractController;
use App\Controller\RequiresDmVersionController;
use App\Entity\Dm\TranchesDoublons;
use App\Entity\Dm\TranchesPretes;
use App\Helper\JsonResponseFromObject;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController implements RequiresDmVersionController
{
    /**
     * @Route(
     *     methods={"GET"},
     *     path="/edges/{publicationCode}/{issueNumbers}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"})
     */
    public function getEdges(string $publicationCode, string $issueNumbers) : JsonResponse
    {
        $qbGetEdges = $this->getEm('dm')->createQueryBuilder();
        $qbGetEdges
            ->select('tranches_pretes')
            ->from(TranchesPretes::class, 'tranches_pretes')
            ->where($qbGetEdges->expr()->eq('tranches_pretes.publicationcode', ':publicationCode'))
            ->setParameter('publicationCode', explode(',', $publicationCode))
            ->andWhere($qbGetEdges->expr()->in('tranches_pretes.issuenumber', ':issueNumbers'))
            ->setParameter('issueNumbers', explode(',', $issueNumbers));

        $edgeResults = $qbGetEdges->getQuery()->getResult();
        return new JsonResponseFromObject($edgeResults);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/edges/references/{publicationCode}/{issueNumbers}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"})
     */
    public function getEdgeReferences(string $publicationCode, string $issueNumbers): JsonResponse
    {
        [$country, $shortPublicationCode] = explode('/', $publicationCode);

        $qbGetReferenceEdges = $this->getEm('dm')->createQueryBuilder();
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
        return new JsonResponseFromObject($edgeResults);
    }
}
