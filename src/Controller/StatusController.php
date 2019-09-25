<?php
namespace App\Controller;

use App\Service\QueryHelperService;
use App\Service\SimilarImagesService;
use Doctrine\DBAL\DBALException;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController
{
    /**
     * @Route(
     *     methods={"GET"},
     *     path="/status/pastec/{pastecHost}",
     *     requirements={"pastecHost"="^(?P<pastec_host_regex>[-_a-z0-9]+)$"},
     *     defaults={"pastecHost"="pastec"}
     * )
     */
    public function getPastecStatus(SimilarImagesService $similarImagesService, string $pastecHost) : Response {
        $log = [];

        try {
            $pastecIndexesImagesNumber = $similarImagesService->getIndexedImagesNumber($pastecHost);
            if ($pastecIndexesImagesNumber > 0) {
                $log[] = "Pastec OK with $pastecIndexesImagesNumber images indexed";
            }
            else {
                throw new RuntimeException('Pastec has no images indexed');
            }
        }
        catch(Exception $e) {
            $error = $e->getMessage();
        }

        $output = implode('<br />', $log);
        if (isset($error)) {
            return new Response($error, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new Response($output);
    }

    /**
     * @Route(methods={"GET"}, path="/status/db"))
     * @throws DBALException
     */
    public function getDbStatus(QueryHelperService $queryHelperService): Response {
        $errors = [];
        $databaseChecks = [
            'dm' => 'SELECT * FROM users LIMIT 1',
            'coa' => $queryHelperService->generateRowCheckOnTables('coa'),
            'coverid' => 'SELECT ID, issuecode, url FROM covers LIMIT 1',
            'dm_stats' => 'SELECT * FROM utilisateurs_histoires_manquantes LIMIT 1',
            'edgecreator' => 'SELECT * FROM edgecreator_modeles2 LIMIT 1'
        ];
        foreach ($databaseChecks as $db=>$dbCheckQuery) {
            $response = $queryHelperService->checkDatabase($dbCheckQuery, $db);
            if ($response !== true) {
                $errors[] = $response;
            }
        }
        if (count($errors) === 0) {
            return new Response('OK for all databases');
        }

        return new Response('<br /><b>'.implode('</b><br /><b>', $errors).'</b>', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @Route(methods={"GET"}, path="/status/pastecsearch/{pastecHost}", defaults={"pastecHost"="pastec"}))
     * @return Response
     */
    public function getPastecSearchStatus(LoggerInterface $logger, SimilarImagesService $similarImagesService, string $pastecHost): Response {
        $log = [];

        try {
            $outputObject = $similarImagesService->getSimilarImages(new File($_ENV['IMAGE_REMOTE_ROOT'].SimilarImagesService::$sampleCover, false), $logger, $pastecHost);
            $matchNumber = count($outputObject->getImageIds());
            if ($matchNumber > 0) {
                $log[] = "Pastec search returned $matchNumber image(s)";
            }
            else {
                throw new RuntimeException('Pastec search returned no image');
            }
        }
        catch(Exception $e) {
            $error = $e->getMessage();
        }

        $output = implode('<br />', $log);
        if (isset($error)) {
            return new Response($error, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new Response($output);
    }
}
