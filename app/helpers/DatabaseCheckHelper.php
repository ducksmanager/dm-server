<?php
namespace DmServer;

use DmServer\Controllers\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DatabaseCheckHelper {
    /**
     * @param $app
     * @param $query
     * @param $db
     * @return Response
     * @internal param $expectedQueryResultsHeader
     */
    static function checkDatabase($app, $query, $db): Response
    {
        $response = AbstractController::callInternal($app, '/rawsql', 'POST', [
            'query' => $query,
            'db' => $db
        ]);

        if (!empty($response)) {
            $objectResponse = json_decode($response->getContent());

            if (is_null($objectResponse)) {
                return new Response('Error for ' . $db. ' : JSON cannot be decoded: ' . $response, 500);
            }

            if (count($objectResponse) > 0) {
                return new Response('OK');
            } else {
                return new Response('Error for ' . $db. ' : received response ' . print_r($objectResponse, true), 500);
            }
        }
        else {
            return new Response('Error : empty response');
        }
    }
}