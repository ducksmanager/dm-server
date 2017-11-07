<?php

namespace DmServer\Controllers\Status;

use DmServer\Controllers\AbstractController;
use DmServer\DatabaseCheckHelper;
use DmServer\DmServer;
use DmServer\SimilarImagesHelper;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/status/pastec',
            /**
             * @codeCoverageIgnore
             */
            function (Application $app) {
                return AbstractController::returnErrorOnException($app, null, function () {
                    $errors = [];
                    $log = [];

                    try {
                        $pastecIndexesImagesNumber = SimilarImagesHelper::getIndexedImagesNumber();
                        if ($pastecIndexesImagesNumber > 0) {
                            $log[] = "Pastec OK with $pastecIndexesImagesNumber images indexed";
                        }
                        else {
                            $errors[] = "Pastec has no images indexed";
                        }
                    }
                    catch(\Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    $output = implode('<br />', $log);
                    if (count($errors) > 0) {
                        if (count($log) > 0) {
                            $output.='<br />';
                        }
                        $output .= '<b>' . implode('</b><br /><b>', $errors) . '</b>';
                    }
                    return new Response($output);
                });
            }
        );

        $routing->get(
            '/status/db',
            /**
             * @codeCoverageIgnore
             */
            function (Application $app, Request $request) {
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
                        $output.='<br /><b>'.implode('</b><br /><b>', $errors).'</b>';
                    }
                    return new Response($output);
                });

            }
        );
    }
}
