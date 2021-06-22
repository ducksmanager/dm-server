<?php

namespace App\Controller;

use App\Entity\Dm\TranchesDoublons;
use App\Entity\Dm\TranchesPretes;
use App\Entity\EdgeCreator\TranchesEnCoursModeles;
use App\Helper\JsonResponseFromObject;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Exception;
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
    public function getEdges(string $publicationCode, string $issueNumbers) : JsonResponse
    {
        [$countryCode, $magazineCode] = explode('/', $publicationCode);

        $qbGetEdges = $this->getEm('dm')->createQueryBuilder();
        $qbGetEdges
            ->select('published_edges.id, published_edges.publicationcode, published_edges.issuenumber')
            ->from(TranchesPretes::class, 'published_edges')
            ->where($qbGetEdges->expr()->eq('published_edges.publicationcode', ':publicationCode'))
            ->setParameter('publicationCode', $publicationCode)
            ->indexBy('published_edges', 'published_edges.issuenumber');

        if (!empty($issueNumbers)) {
            $qbGetEdges
                ->andWhere($qbGetEdges->expr()->in('published_edges.issuenumber', ':issueNumbers'))
                ->setParameter('issueNumbers', explode(',', $issueNumbers));
        }

        $publishedEdgesResults = (array) $qbGetEdges->getQuery()->getArrayResult();

        $qbGetEdgeModels = $this->getEm('edgecreator')->createQueryBuilder();
        $qbGetEdgeModels
            ->select('edge_models.id, edge_models.numero')
            ->from(TranchesEnCoursModeles::class, 'edge_models')
            ->andWhere($qbGetEdges->expr()->eq('edge_models.pays', ':countryCode'))
            ->setParameter('countryCode', $countryCode)
            ->andWhere($qbGetEdges->expr()->eq('edge_models.magazine', ':magazineCode'))
            ->setParameter('magazineCode', $magazineCode)
            ->indexBy('edge_models', 'edge_models.numero');

        $edgeModels = $qbGetEdgeModels->getQuery()->getArrayResult();

        foreach($publishedEdgesResults as $issueNumber => $publishedEdgesResult) {
            if (array_key_exists($issueNumber, $edgeModels)) {
                $publishedEdgesResults[$issueNumber]['modelId'] = $edgeModels[$issueNumber]['id'];
            }
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

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/edges/wanted"
     * )
     * @throws Exception
     */
    public function getMostWantedEdges() : JsonResponse {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('numberOfIssues', 'numberOfIssues', 'integer')
            ->addScalarResult('publicationcode', 'publicationcode')
            ->addScalarResult('issuenumber', 'issuenumber');

        $mostWantedQuery = "
            SELECT Count(Numero) as numberOfIssues, CONCAT(Pays,'/',Magazine) AS publicationcode, Numero AS issuenumber
            FROM numeros
            WHERE NOT EXISTS(
                SELECT 1
                FROM tranches_pretes
                WHERE CONCAT(numeros.Pays, '/', numeros.Magazine) = tranches_pretes.publicationcode
                  AND numeros.Numero_nospace = tranches_pretes.issuenumber
            )
            GROUP BY Pays,Magazine,Numero
            ORDER BY numberOfIssues DESC, Pays, Magazine, Numero
            LIMIT 20
        ";
        return new JsonResponse($this->getEm('dm')->createNativeQuery($mostWantedQuery, $rsm)->getArrayResult());
    }
}
