<?php

namespace App\Controller;

use App\Entity\Dm\TranchesDoublons;
use App\Entity\Dm\TranchesPretes;
use App\Entity\EdgeCreator\TranchesEnCoursModeles;
use App\Helper\JsonResponseFromObject;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EdgesController extends AbstractController implements RequiresDmVersionController
{
    /**
     * @Route(
     *     methods={"GET"},
     *     path="/edges/{publicationCode}/{issueNumbers}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"},
     *     defaults={"issueNumbers"=""}
     * )
     */
    public function getEdges(string $publicationCode, string $issueNumbers, LoggerInterface $logger) : JsonResponse
    {
        $qbGetEdges = $this->getEm('dm')->createQueryBuilder();
        $qbGetEdges
            ->select('published_edges')
            ->from(TranchesPretes::class, 'published_edges')
            ->where($qbGetEdges->expr()->eq('published_edges.publicationcode', ':publicationCode'))
            ->setParameter('publicationCode', $publicationCode)
            ->indexBy('published_edges', 'published_edges.issuenumber');

        [$countryCode, $magazineCode] = explode('/', $publicationCode);
        $qbGetEditableEdges = $this->getEm('edgecreator')->createQueryBuilder();
        $qbGetEditableEdges
            ->select('editable_edges.numero')
            ->from(TranchesEnCoursModeles::class, 'editable_edges')
            ->andWhere($qbGetEdges->expr()->eq('editable_edges.pays', ':countryCode'))
            ->setParameter('countryCode', $countryCode)
            ->andWhere($qbGetEdges->expr()->eq('editable_edges.active', $qbGetEdges->expr()->literal(false)))
            ->andWhere($qbGetEdges->expr()->eq('editable_edges.magazine', ':magazineCode'))
            ->setParameter('magazineCode', $magazineCode)
            ->indexBy('editable_edges', 'editable_edges.numero');

        if (!empty($issueNumbers)) {
            $qbGetEdges
                ->andWhere($qbGetEdges->expr()->in('published_edges.issuenumber', ':issueNumbers'))
                ->setParameter('issueNumbers', explode(',', $issueNumbers));
            $qbGetEditableEdges
                ->andWhere($qbGetEditableEdges->expr()->in('editable_edges.numero', ':issueNumbers'))
                ->setParameter('issueNumbers', explode(',', $issueNumbers));
        }
        $publishedEdgesResults = (array) $qbGetEdges->getQuery()->getArrayResult();
        $editableEdgesResults = $qbGetEditableEdges->getQuery()->getResult();

        foreach (array_keys($editableEdgesResults) as $editableIssueNumber) {
            $publishedEdgesResults[$editableIssueNumber]['editable'] = true;
        }
        return new JsonResponseFromObject(array_values($publishedEdgesResults));
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
