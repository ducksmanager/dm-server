<?php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\ResultStatement;
use Psr\Log\LoggerInterface;

class QueryHelperService {
    /** @var LoggerInterface $logger */
    private static LoggerInterface $logger;

    /** @var ManagerRegistry $emRegistry */
    private static ManagerRegistry $emRegistry;

    public function __construct(ManagerRegistry $emRegistry, LoggerInterface $logger)
    {
        self::$logger = $logger;
        self::$emRegistry = $emRegistry;
    }

    public static function checkDatabase(string $query, string $dbName) {
        $connection = self::$emRegistry->getManager($dbName)->getConnection();
        $results = $connection->fetchAllAssociative($query, []);
        if (is_array($results) && count($results) > 0) {
            self::$logger->info("DB check for $dbName was successful");
            return true;
        }

        $responseText = json_encode($results);
        self::$logger->info("DB check for $dbName failed because no data could be fetched");
        return "Error for $dbName : received response $responseText";
    }

    /**
     * @param string $emName
     * @return string
     */
    public static function generateRowCheckOnTables(string $emName): string {
        /** @var Connection $connection */
        $connection = self::$emRegistry->getManager($emName)->getConnection();
        $emTables = $connection->getSchemaManager()->listTableNames();

        $tableCounts = implode(' UNION ', array_map(fn($tableName) => "SELECT '$tableName' AS table_name, COUNT(*) AS cpt FROM $tableName", $emTables));
        return
            "SELECT * FROM (
              SELECT count(*) AS counter FROM ($tableCounts) db_tables 
              WHERE db_tables.cpt > 0
            ) AS non_empty_tables WHERE non_empty_tables.counter = " . count($emTables);
    }
}
