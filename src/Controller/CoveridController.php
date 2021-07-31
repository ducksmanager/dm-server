<?php

namespace App\Controller;

use App\Entity\Coverid\Covers;
use App\Service\SimilarImagesService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class CoveridController extends AbstractController
{
    public static string $uploadFileName = 'wtd_jpg';
    public static string $uploadDestination = '/tmp';

    /**
     * @param QueryBuilder $qb
     * @return Func
     */
    public static function getFullUrlFunc(QueryBuilder $qb): Func
    {
        return new Func('CONCAT', [
            $qb->expr()->literal($_ENV['IMAGE_REMOTE_ROOT']),
            'covers.sitecode',
            $qb->expr()->literal('/'),
            'case covers.sitecode when \'webusers\' then \'webusers/\' else \'\' end',
            'covers.url'
        ]);
    }

    /**
     * @Route(methods={"GET"}, path="/cover-id/download/{coverId}")
     * @throws NonUniqueResultException
     */
    public function downloadCover(int $coverId) : Response {
        $coverEm = $this->getEm('coverid');
        $qb = $coverEm->createQueryBuilder();

        $qb
            ->select(
                'covers.url',
                self::getFullUrlFunc($qb). 'as full_url')
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
     * @throws Exception
     */
    public function searchCover(Request $request, LoggerInterface $logger, SimilarImagesService $similarImagesService): Response
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
        $targetFileName = random_int(0, 1000000) . '.jpg';
        $file = $uploadedFile->move(self::$uploadDestination, $targetFileName);
        $logger->info('Cover ID search: upload file moving done');

        $engineResponse = $similarImagesService->getSimilarImages($file, $logger);
        @unlink("/tmp/$targetFileName");

        $logger->info('Cover ID search: processing done');

        if (is_null($engineResponse)) {
            return new Response('Pastec returned NULL', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (empty($engineResponse->getImageIds())) {
            return new JsonResponse([
                'issues' => [],
                'imageIds' => [],
                'type' => $engineResponse->getType()
            ]);
        }

        $coverIdsList = $engineResponse->getImageIds();
        $coverIds = implode(',', $coverIdsList);
        $logger->info("Cover ID search: matched cover IDs $coverIds");
        $logger->info('Cover ID search: scores='.json_encode($engineResponse->getScores()));

        $coverInfos = $this->getIssuesCodesFromCoverIds($coverIdsList);
        uksort($coverInfos, fn(string $issuecode1, string $issuecode2) =>
            array_search($coverInfos[$issuecode1]['coverid'], $coverIdsList, false) <=>
            array_search($coverInfos[$issuecode2]['coverid'], $coverIdsList, false)
        );

        $foundIssueCodes = array_keys($coverInfos);
        $logger->info('Cover ID search: matched issue codes ' . implode(',', $foundIssueCodes));

        $issueCodes = implode(',', array_unique($foundIssueCodes));

        $issues = json_decode(
            $this->callService(CoaController::class, 'listIssuesFromIssueCodes', compact('issueCodes'))->getContent(),
            true
        );
        $logger->info('Cover ID search: matched ' . count($coverInfos) . ' issues');

        uksort($issues, fn(string $issuecode1, string $issuecode2) =>
            array_search($issuecode1, $foundIssueCodes, false) <=>
            array_search($issuecode2, $foundIssueCodes, false)
        );
        return new JsonResponse([
            'issues' => (object) array_values($issues),
            'imageIds' => $engineResponse->getImageIds()
        ]);
    }

    private function getIssuesCodesFromCoverIds(array $coverIds): array
    {
        $coverEm = $this->getEm('coverid');

        $qb = $coverEm->createQueryBuilder();
        $qb
            ->select('covers.issuecode, covers.url, covers.id AS coverid')
            ->from(Covers::class, 'covers');

        $qb
            ->where($qb->expr()->in('covers.id', $coverIds))
            ->indexBy('covers', 'covers.issuecode');

        return $qb->getQuery()->getResult();
    }
}
