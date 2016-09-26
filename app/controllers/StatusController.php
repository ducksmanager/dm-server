<?php

namespace Wtd;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            function (Application $app, Request $request, $serverIp, $serverPort) {
                return AppController::return500ErrorOnException(function() use ($serverIp, $serverPort) {
                    $rawSqlUserRole = 'rawsql';
                    $credentialsForRawSql = Wtd::getAppRoles()[$rawSqlUserRole];
                    $rawSqlUserPassword = explode(':', $credentialsForRawSql)[1];

                    $context = stream_context_create([
                        'http' => [
                            'method' => 'POST',
                            'header' => implode("\r\n",
                                [
                                    'Authorization: Basic ' . base64_encode($rawSqlUserRole . ':' . $rawSqlUserPassword),
                                    'Content-Type: application/x-www-form-urlencoded',
                                    'Cache-Control: no-cache',
                                    'x-wtd-version: 1.0',
                                ]),
                            'content' => http_build_query(
                                [
                                    'query' => 'SELECT * FROM inducks_country',
                                    'db' => 'db_coa'
                                ]
                            )
                        ]
                    ]);
                    $handle = fopen('http://' . $serverIp . ':' . $serverPort . '/wtd-server/rawsql', "r", null,
                        $context);

                    if ($handle) {
                        $buffer = "";
                        while (!feof($handle)) {
                            $buffer .= fgets($handle, 4096);
                        }
                        fclose($handle);
                        $objectResponse = json_decode($buffer, true);

                        if (count($objectResponse) > 1
                            && array_keys($objectResponse[0]) === ['countrycode', 'countryname', 'defaultlanguage']
                        ) {
                            return new Response('OK');
                        } else {
                            return new Response('Error' . "\n\n" . print_r($objectResponse, true), 500);
                        }
                    }
                    return new Response('Error', 500);
                });

            }
        )->assert('serverIp', '^[.\d]+$')->assert('serverPort', '^[\d]+$');
    }
}
