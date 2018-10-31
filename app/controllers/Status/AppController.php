<?php

namespace DmServer\Controllers\Status;

use DmServer\Controllers\AbstractController;
use DmServer\DatabaseCheckHelper;
use DmServer\DmServer;
use DmServer\SimilarImagesHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Radebatz\Silex2Swagger\Swagger\Annotations as S2S;
use Swagger\Annotations as SWG;

/**
 * @S2S\Controller(prefix="/status",
 *   @SWG\Parameter(
 *     name="x-dm-version",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Response(response=200),
 *   @SWG\Response(response="default", description="Error")
 * ),
 * @SLX\Before("DmServer\RequestInterceptor::checkVersion")
 */
class AppController extends AbstractController
{
    public static $sampleCover = "https://outducks.org/au/bp/001/au_bp_001a_001.jpg";

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="swagger.json")
     * )
     * @codeCoverageIgnore
     * @param Application $app
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function getSwaggerJson(Application $app) {
        return AbstractController::returnErrorOnException($app, null, function () {
            try {
                $swaggerContent = file_get_contents(DmServer::$settings['swagger_path']);
                if ($swaggerContent !== false) {
                    $response = new Response($swaggerContent);

                    $disposition = $response->headers->makeDisposition(
                        ResponseHeaderBag::DISPOSITION_INLINE,
                        'swagger.json'
                    );

                    $response->headers->set('Content-Disposition', $disposition);

                    return $response;
                }
            }
            catch(\Exception $e) {}

            return new Response('swagger.json not found', Response::HTTP_NOT_FOUND);
        });
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="pastec/{pastecHost}"),
     *   @SWG\Parameter(
     *     name="pastecHost",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="pastecHost", regex="^(?P<pastec_host_regex>[-_a-z0-9]+)$"),
     *	 @SLX\Value(variable="pastecHost", default="pastec")
     * )
     * @codeCoverageIgnore
     * @param Application $app
     * @param string $pastecHost
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function getPastecStatus(Application $app, $pastecHost) {
        return AbstractController::returnErrorOnException($app, null, function () use ($pastecHost) {
            $log = [];

            try {
                $pastecIndexesImagesNumber = SimilarImagesHelper::getIndexedImagesNumber($pastecHost);
                if ($pastecIndexesImagesNumber > 0) {
                    $log[] = "Pastec OK with $pastecIndexesImagesNumber images indexed";
                }
                else {
                    throw new \Exception('Pastec has no images indexed');
                }
            }
            catch(\Exception $e) {
                $error = $e->getMessage();
            }

            $output = implode('<br />', $log);
            if (isset($error)) {
                return new Response($error, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return new Response($output);
        });
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="pastecsearch/{pastecHost}"),
     *   @SWG\Parameter(
     *     name="pastecHost",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="pastecHost", regex="^(?P<pastec_host_regex>[-_a-z0-9]+)$"),
     *	 @SLX\Value(variable="pastecHost", default="pastec")
     * )
     * @codeCoverageIgnore
     * @param Application $app
     * @param string $pastecHost
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function getPastecSearchStatus(Application $app, $pastecHost) {
        return AbstractController::returnErrorOnException($app, null, function () use ($app, $pastecHost) {
            $log = [];

            try {
                $outputObject = SimilarImagesHelper::getSimilarImages(new File(self::$sampleCover, false), $app['monolog'], $pastecHost);
                $matchNumber = count($outputObject->getImageIds());
                if ($matchNumber > 0) {
                    $log[] = "Pastec search returned $matchNumber image(s)";
                }
                else {
                    throw new \Exception('Pastec search returned no image');
                }
            }
            catch(\Exception $e) {
                $error = $e->getMessage();
            }

            $output = implode('<br />', $log);
            if (isset($error)) {
                return new Response($error, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return new Response($output);
        });
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="db")
     * )
     * @param Application $app
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function getDbStatus(Application $app) {
        return AbstractController::returnErrorOnException($app, null, function () use ($app) {
            $errors = [];
            $log = [];
            self::setClientVersion($app, '1.0.0');

            $databaseChecks = [
                [
                    'db' => DmServer::CONFIG_DB_KEY_DM,
                    'query' => 'SELECT * FROM users LIMIT 1'
                ],
                [
                    'db' => DmServer::CONFIG_DB_KEY_COA,
                    'query' => DatabaseCheckHelper::generateRowCheckOnTables(DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA))
                ],
                [
                    'db' => DmServer::CONFIG_DB_KEY_COVER_ID,
                    'query' => 'SELECT ID, issuecode, url FROM covers LIMIT 1'
                ],
                [
                    'db' => DmServer::CONFIG_DB_KEY_DM_STATS,
                    'query' => 'SELECT * FROM utilisateurs_histoires_manquantes LIMIT 1'
                ],
                [
                    'db' => DmServer::CONFIG_DB_KEY_EDGECREATOR,
                    'query' => 'SELECT * FROM edgecreator_modeles2 LIMIT 1'
                ]
            ];

            foreach ($databaseChecks as $dbCheck) {
                $response = DatabaseCheckHelper::checkDatabase($app, $dbCheck['query'], $dbCheck['db']);
                if ($response->getStatusCode() !== Response::HTTP_OK) {
                    $errors[] = $response->getContent();
                }
            }

            if (count($errors) === 0) {
                $log[] = 'OK for all databases';
            }

            $output = implode('<br />', $log);
            if (count($errors) > 0) {
                return new Response('<br /><b>'.implode('</b><br /><b>', $errors).'</b>', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return new Response($output);
        });
    }
}
