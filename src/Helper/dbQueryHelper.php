<?php
namespace App\Helper;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

trait dbQueryHelper {
    /**
     * @param string               $query
     * @param EntityManager        $em
     * @param array                $parameters
     * @param LoggerInterface|null $logger
     * @return \Doctrine\DBAL\Driver\ResultStatement|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function runQueryNoCheck(string $query, EntityManager $em, array $parameters, LoggerInterface $logger = null) {
        if (stripos(trim($query), 'SELECT') === 0) {
            $results = $em->getConnection()->fetchAll($query, $parameters);
        }
        else {
            $results = $em->getConnection()->executeQuery($query, $parameters);
        }

        if (!is_null($logger)) {
            $logger->info("Raw sql sent: $query with ".print_r($parameters, true));
        }
        return $results;
    }

    /**
     * @param LoggerInterface $logger
     * @param string          $query
     * @param string          $dbName
     * @param EntityManager   $em
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function checkDatabase(LoggerInterface $logger, string $query, string $dbName, EntityManager $em) {
        $results = self::runQueryNoCheck($query, $em, []);
        if (is_array($results) && count($results) > 0) {
            $logger->info("DB check for $dbName was successful");
            return true;
        }

        $responseText = json_encode($results);
        $logger->info("DB check for $dbName failed because no data could be fetched");
        return "Error for $dbName : received response $responseText";
    }

    /**
     * @param EntityManager $em
     * @return string
     */
    public static function generateRowCheckOnTables(EntityManager $em): string {
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
