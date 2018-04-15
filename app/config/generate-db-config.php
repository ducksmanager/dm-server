<?php
require_once __DIR__. '/../../vendor/autoload.php';
require_once __DIR__. '/../DmServer.php';

use Symfony\Component\Yaml\Yaml;

$dbServiceFilter = function ($serviceKey) {
    return strpos($serviceKey, 'db_') === 0;
};

$composeFile = Yaml::parse(file_get_contents(__DIR__.'/../../docker-compose.yml'));
$configuredEntityManagerNames = \DmServer\DmServer::$configuredEntityManagerNames;

try {
    if (isset($composeFile['services'])) {
        $dbServices = array_filter($composeFile['services'], $dbServiceFilter, ARRAY_FILTER_USE_KEY);
        if (count($dbServices) > 0) {
            $dbConfigs = [];

            foreach ($dbServices as $dbEntityManagerName => $dbService) {
                if (in_array($dbEntityManagerName, $configuredEntityManagerNames)) {
                    $dbConfigs[$dbEntityManagerName] = implode("\n", [
                        '[' . $dbEntityManagerName . ']',
                        implode('=', ['host', $dbService['container_name']]),
                        implode('=', ['type', 'mysql']),
                        implode('=', ['port', 3306]),
                        implode('=', ['dbname', $dbService['environment']['MYSQL_DATABASE']]),
                        implode('=', ['username', 'root']),
                        implode('=', ['password', $dbService['environment']['MYSQL_ROOT_PASSWORD']]),
                    ]);
                } else {
                    throw new InvalidArgumentException("DB service name is invalid : $dbEntityManagerName but it should be one of [" . implode(',', $configuredEntityManagerNames) . ']');
                }
            }
            file_put_contents(__DIR__ . '/config.db.ini', implode("\n\n", $dbConfigs));
        } else {
            throw new RuntimeException('No DB service found');
        }
    } else {
        throw new RuntimeException('No services found');
    }
}
catch(Exception $e) {
    echo $e->getMessage();
    return 1;
}



