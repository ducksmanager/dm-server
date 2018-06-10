<?php
require_once __DIR__. '/../../vendor/autoload.php';
require_once __DIR__. '/../DmServer.php';

use Symfony\Component\Yaml\Yaml;

$filesToCopy = ['roles.base.ini' => 'roles.ini', 'settings.base.ini' => 'settings.ini'];
foreach($filesToCopy as $source => $destination) {
    if (is_file(__DIR__.'/'.$destination)) {
        echo "Configuration file $destination already exists.\n";
    }
    else {
        if (copy(__DIR__.'/'.$source, __DIR__.'/'.$destination)) {
            echo "Configuration file $destination was created from file $source.\n";
        }
        else {
            echo "Error : Configuration file $destination wouldn't be created from file $source.\n";
        }
    }
}

$composeConfig = [];
foreach(array_slice($argv, 1) as $configFile) {
    $composeConfig = array_merge($composeConfig, Yaml::parse(file_get_contents(getcwd()."/$configFile")));
}

$configuredEntityManagerNames = \DmServer\DmServer::$configuredEntityManagerNames;

try {
    if (isset($composeConfig['services'])) {
        $dbServices = array_filter($composeConfig['services'], function ($serviceKey) {
            return strpos($serviceKey, 'db_') === 0;
        }, ARRAY_FILTER_USE_KEY);

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
                        implode('=', ['password', $composeConfig['MYSQL_ROOT_PASSWORD']]),
                    ]);
                } else {
                    throw new InvalidArgumentException("DB service name is invalid : $dbEntityManagerName but it should be one of [" . implode(',', $configuredEntityManagerNames) . "]\n");
                }
            }
            file_put_contents(__DIR__ . '/config.db.ini', implode("\n\n", $dbConfigs));
            echo "The DB config has been generated.\n";
        } else {
            throw new RuntimeException("No DB service found\n");
        }
    } else {
        throw new RuntimeException("No services found\n");
    }
}
catch(Exception $e) {
    echo $e->getMessage();
    return 1;
}



