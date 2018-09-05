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
     * @throws \InvalidArgumentException
     */
    public static function checkDatabase($app, $query, $db): Response
    {
        $response = AbstractController::callInternal($app, '/rawsql', 'POST', [
            'query' => $query,
            'db' => $db,
            'log' => 0
        ]);

        $objectResponse = json_decode($response->getContent());

        if (is_null($objectResponse)) {
            return new Response("Error for $db : JSON cannot be decoded: $response", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (count($objectResponse) > 0) {
            $app['monolog']->addInfo("DB check for $db was successful");
            return new Response('OK');
        } else {
            $responseText = print_r($objectResponse, true);
            $app['monolog']->addInfo("DB check for $db failed with error $responseText");
            return new Response("Error for $db : received response $responseText", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param EntityManager $em
     * @return string
     */
    public static function generateRowCheckOnTables(EntityManager $em)
    {
        $emTables = $em->getConnection()->getSchemaManager()->listTableNames();

        $tableCounts = implode(' UNION ', array_map(function ($tableName) {
            return "SELECT '$tableName' AS table_name, COUNT(*) AS cpt FROM $tableName";
        }, $emTables));

        return
            "SELECT * FROM (
              SELECT count(*) AS counter FROM ($tableCounts) db_tables 
              WHERE db_tables.cpt > 0
            ) AS non_empty_tables WHERE non_empty_tables.counter = " . count($emTables);
    }
}
