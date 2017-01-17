<?php

namespace DmServer;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends AppController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/status/{serverIp}/{serverPort}',
            /**
             * @codeCoverageIgnore
             */
            function (Application $app, Request $request, $serverIp, $serverPort) {
                return AppController::return500ErrorOnException($app, function() use ($serverIp, $serverPort) {

                    $databaseChecks = [
                        [
                            'db' => DmServer::CONFIG_DB_KEY_DM,
                            'query' => 'SELECT * FROM bibliotheque_ordre_magazines LIMIT 1',
                            'expectedQueryResultsHeader' => ['Pays', 'Magazine', 'Ordre', 'ID_Utilisateur']

                        ], [
                            'db' => DmServer::CONFIG_DB_KEY_COA,
                            'query' => 'SELECT * FROM inducks_country LIMIT 1',
                            'expectedQueryResultsHeader' => ['countrycode', 'countryname', 'defaultlanguage']
                        ], [
                            'db' => DmServer::CONFIG_DB_KEY_COVER_ID,
                            'query' => 'SELECT * FROM covers LIMIT 1',
                            'expectedQueryResultsHeader' => ['ID', 'issuecode', 'sitecode', 'url']
                        ], [
                            'db' => DmServer::CONFIG_DB_KEY_DM_STATS,
                            'query' => 'SELECT * FROM utilisateurs_histoires_manquantes LIMIT 1',
                            'expectedQueryResultsHeader' => ['ID_User', 'personcode', 'storycode']
                        ]
                    ];

                    $errors = [];
                    foreach($databaseChecks as $dbCheck) {
                        $response = DatabaseCheckHelper::checkDatabase($serverIp, $serverPort, $dbCheck['expectedQueryResultsHeader'], $dbCheck['query'], $dbCheck['db']);
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
        )->assert('serverIp', '^.+$')->assert('serverPort', '^[\d]+$');
    }
}
