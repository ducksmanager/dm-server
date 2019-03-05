<?php

namespace App\Controller\Coverid;

use App\Controller\AbstractController;
use App\Entity\Coverid\Covers;
use App\Helper\SimilarImagesHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Func;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public static $uploadFileName = 'wtd_jpg';
    public static $uploadDestination = ['/tmp', 'test.jpg'];

    /**
     * @Route(methods={"GET"}, path="/cover-id/download/{coverId}")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function downloadCover(int $coverId) : Response {
        /** @var EntityManager $coverEm */
        $coverEm = $this->container->get('doctrine')->getManager('coverid');
        $qb = $coverEm->createQueryBuilder();

        $concatFunc = new Func('CONCAT', [
            $qb->expr()->literal($_ENV['IMAGE_REMOTE_ROOT']),
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

        $localFilePath = $_ENV['IMAGE_LOCAL_ROOT'] . basename($url);
        @mkdir($_ENV['IMAGE_LOCAL_ROOT'] . dirname($url), 0777, true);
        file_put_contents(
            $localFilePath,
            file_get_contents(
                $fullUrl,
                false,
                stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ])
            )
        );
        
        $response = new Response(file_get_contents($localFilePath));

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'cover.jpg'
        );

        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    /**
     * @Route(methods={"POST"}, path="/cover-id/search")
     * @return Response
     */
    public function searchCover(Request $request, LoggerInterface $logger): Response
    {
        $logger->info('Cover ID search: start');
        if (($nbUploaded = $request->files->count()) !== 1) {
            return new Response('Invalid number of uploaded files : should be 1, was ' . $nbUploaded,
                Response::HTTP_BAD_REQUEST);
        }

        /** @var File $uploadedFile */
        $uploadedFile = $request->files->get(self::$uploadFileName);
        if (is_null($uploadedFile)) {
            return new Response('Invalid upload file : expected file name ' . self::$uploadFileName,
                Response::HTTP_BAD_REQUEST);
        }

        $logger->info('Cover ID search: upload file validation done');
        $file = $uploadedFile->move(self::$uploadDestination[0], self::$uploadDestination[1]);
        $logger->info('Cover ID search: upload file moving done');

        $engineResponse = SimilarImagesHelper::getSimilarImages($file, $logger);

        $logger->info('Cover ID search: processing done');

        if (is_null($engineResponse) || count($engineResponse->getImageIds()) === 0) {
            return new JsonResponse(['type' => $engineResponse->getType()]);
        }

        $coverIds = implode(',', $engineResponse->getImageIds());
        $logger->info("Cover ID search: matched cover IDs $coverIds");
        $coverInfos = $this->getIssuesCodesFromCoverIds(explode(',', $coverIds));

        $foundIssueCodes = array_map(function ($coverInfo) {
            return $coverInfo['issuecode'];
        }, $coverInfos);
        $logger->info('Cover ID search: matched issue codes ' . implode(',', $foundIssueCodes));

        $issuesWithSameCover = [];

        $issueCodes = implode(',',
            array_unique(
                array_merge(
                    $foundIssueCodes,
                    array_map(/**
                     * @param \stdClass $issue
                     * @return string
                     */
                        function (\stdClass $issue) {
                            return $issue->issuecode;
                        }, $issuesWithSameCover
                    )
                )
            )
        );

        $issues = $this->callService(\App\Controller\Coa\AppController::class, 'listIssuesFromIssueCodes', compact('issueCodes'))->getContent();
        $logger->info('Cover ID search: matched ' . count($coverInfos) . ' issues');

        return new JsonResponse([
            'issues' => json_decode($issues),
            'imageIds' => $engineResponse->getImageIds()
        ]);
    }

    /**
     * @Route(methods={"GET"}, path="/cover-id/issuecodes/{coverIds}")
     */
    public function getCoverList(string $coverIds): Response
    {
        return new JsonResponse(
            $this->getIssuesCodesFromCoverIds(explode(',', $coverIds))
        );
    }

    private function getIssuesCodesFromCoverIds(array $coverIds): array
    {
        /** @var EntityManager $coverEm */
        $coverEm = $this->container->get('doctrine')->getManager('coverid');

        $qb = $coverEm->createQueryBuilder();
        $qb
            ->select('covers.issuecode, covers.url')
            ->from(Covers::class, 'covers');

        $qb->where($qb->expr()->in('covers.id', $coverIds));

        $results = $qb->getQuery()->getResult();

        array_walk(
            $results,
            function ($cover, $i) use ($coverIds, &$coverInfos) {
                $coverInfos[$coverIds[$i]] = ['url' => $cover['url'], 'issuecode' => $cover['issuecode']];
            }
        );

        return $coverInfos;
    }
}
