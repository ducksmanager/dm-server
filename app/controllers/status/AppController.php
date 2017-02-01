<?php

namespace DmServer\Controllers\Status;

use DmServer\Controllers\AbstractController;
use DmServer\DatabaseCheckHelper;
use DmServer\DmServer;
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
            '/status',
            /**
             * @codeCoverageIgnore
             */
            function (Application $app, Request $request) {
                return AbstractController::return500ErrorOnException($app, function() use ($app) {

                    self::setClientVersion($app, '1.0.0');

                    $databaseChecks = [
                        [
                            'db' => DmServer::CONFIG_DB_KEY_DM,
                            'query' => 'SELECT * FROM users LIMIT 1'

                        ], [
                            'db' => DmServer::CONFIG_DB_KEY_COA,
                            'query' => 'SELECT * FROM inducks_countryname LIMIT 1'
                        ], [
                            'db' => DmServer::CONFIG_DB_KEY_COVER_ID,
                            'query' => 'SELECT ID, issuecode, url FROM covers LIMIT 1'
                        ], [
                            'db' => DmServer::CONFIG_DB_KEY_DM_STATS,
                            'query' => 'SELECT * FROM utilisateurs_histoires_manquantes LIMIT 1'
                        ]
                    ];

                    $errors = [];
                    foreach($databaseChecks as $dbCheck) {
                        $response = DatabaseCheckHelper::checkDatabase($app, $dbCheck['query'], $dbCheck['db']);
                        if ($response->getStatusCode() !== Response::HTTP_OK) {
                            $errors[] = $response->getContent();
                        }
                    }

                    if (count($errors) > 0) {
                        return new Response(implode('<br />', $errors));
                    }
                    else {
                        return new Response('OK for all databases');
                    }
                });

            }
        );
    }
}
