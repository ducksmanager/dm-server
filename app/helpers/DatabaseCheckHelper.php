<?php
namespace DmServer;

use DmServer\Controllers\AbstractController;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class DatabaseCheckHelper {
    /**
     * @param $app
     * @param $query
     * @param $db
     * @return Response
     * @internal param $expectedQueryResultsHeader
     */
    public static function checkDatabase($app, $query, $db): Response
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

    /**
     * @param EntityManager $em
     * @return string
     */
    public static function generateRowCheckOnTables(EntityManager $em)
    {
        $emTables = $em->getConnection()->getSchemaManager()->listTableNames();

        return
            "SELECT * FROM (
              SELECT count(*) AS counter FROM ("
                . implode(" UNION ", array_map(function ($tableName) {
                    return "SELECT '$tableName' AS table_name, COUNT(*) AS cpt FROM $tableName";
                }, $emTables))
                . ") db_tables 
                WHERE db_tables.cpt > 0
            ) AS non_empty_tables WHERE non_empty_tables.counter = " . count($emTables) . "
            ";
    }
}