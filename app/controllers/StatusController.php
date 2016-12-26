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
                return AppController::return500ErrorOnException($app, function() use ($serverIp, $serverPort) {
                    $rawSqlUserRole = 'rawsql';
                    $credentialsForRawSql = Wtd::getAppRoles()[$rawSqlUserRole];
                    $rawSqlUserPassword = explode(':', $credentialsForRawSql)[1];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://' . $serverIp . ':' . $serverPort . '/dm-server/rawsql');
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
                        [
                            'query' => 'SELECT * FROM inducks_country',
                            'db' => 'db_coa'
                        ]
                    ));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Authorization: Basic ' . base64_encode($rawSqlUserRole . ':' . $rawSqlUserPassword),
                        'Content-Type: application/x-www-form-urlencoded',
                        'Cache-Control: no-cache',
                        'x-dm-version: 1.0',
                    ));

                    $buffer = curl_exec($ch);
                    curl_close($ch);

                    if ($buffer) {
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
        )->assert('serverIp', '^.+$')->assert('serverPort', '^[\d]+$');
    }
}
