<?php
require_once __DIR__."/../../vendor/autoload.php";
set_include_path(implode(PATH_SEPARATOR, array(get_include_path(),__DIR__.'/../Wtd.php')));

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Wtd\Wtd;

$config = parse_ini_file(__DIR__.'/config.ini', true);

$metaDataConfig = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../models"), true, null, null, FALSE);
$connectionParams = Wtd::getConnectionParams($config);
$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $metaDataConfig);
$conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

$em = EntityManager::create($conn, $metaDataConfig);

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
