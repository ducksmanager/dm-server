<?php
require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/../DmServer.php";

use Symfony\Component\Yaml\Yaml;

$dbServiceFilter = function ($serviceKey) {
    return strpos($serviceKey, 'db_') === 0;
};

$composeFile = Yaml::parse(file_get_contents(__DIR__.'/../../docker-compose.yml'));
$configuredEntityManagerNames = \DmServer\DmServer::$configuredEntityManagerNames;

if (isset($composeFile['services'])) {
    $dbServices = array_filter($composeFile['services'], $dbServiceFilter, ARRAY_FILTER_USE_KEY);
    if (count($dbServices) > 0) {
        $dbConfigs = [];

        foreach($dbServices as $dbEntityManagerName => $dbService) {
            if (in_array($dbEntityManagerName, $configuredEntityManagerNames)) {
                $dbConfigs[$dbEntityManagerName] = implode("\n", [
                    '['.$dbEntityManagerName.']',
                    implode('=', ['host', $dbService['container_name']]),
                    implode('=', ['type', 'mysql']),
                    implode('=', ['port', 3306]),
                    implode('=', ['dbname', $dbService['environment']['MYSQL_DATABASE']]),
                    implode('=', ['username', 'root']),
                    implode('=', ['password', $dbService['environment']['MYSQL_ROOT_PASSWORD']]),
                ]);
            }
            else {
                echo "DB service name is invalid : $dbEntityManagerName but it should be one of [".implode(',', $configuredEntityManagerNames) ."]";
                exit(1);
            }
        }

        file_put_contents(__DIR__.'/config.db.ini', implode("\n\n", $dbConfigs));
    }
    else {
        echo "No DB service found\n";
        exit(1);
    }
}
else {
    echo "No services found\n";
    exit(1);
}



