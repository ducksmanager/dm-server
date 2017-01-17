<?php
namespace DmServer;

use Symfony\Component\HttpFoundation\Response;

class DatabaseCheckHelper {
    /**
     * @param $serverIp
     * @param $serverPort
     * @param $expectedQueryResultsHeader
     * @param $query
     * @param $db
     * @return Response
     */
    static function checkDatabase($serverIp, $serverPort, $expectedQueryResultsHeader, $query, $db): Response
    {
        $rawSqlUserRole = 'rawsql';
        $credentialsForRawSql = DmServer::getAppRoles()[$rawSqlUserRole];
        $rawSqlUserPassword = explode(':', $credentialsForRawSql)[1];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://' . $serverIp . ':' . $serverPort . '/dm-server/rawsql');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
            [
                'query' => $query,
                'db' => $db
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

            if (is_null($objectResponse)) {
                return new Response('Error for ' . $db. ' : JSON cannot be decoded: ' . $buffer, 500);
            }

            if (count($objectResponse) > 0
                && array_keys($objectResponse[0]) === $expectedQueryResultsHeader
            ) {
                return new Response('OK');
            } else {
                return new Response('Error for ' . $db. ' : received response ' . print_r($objectResponse, true), 500);
            }
        }
        else {
            return new Response('Error ' . $db. ' : ' . curl_error($ch), 500);
        }
    }
}