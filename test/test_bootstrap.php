<?php
$loader = require __DIR__ . '/../vendor/autoload.php';

foreach([\Wtd\Wtd::CONFIG_DB_KEY_DM, \Wtd\Wtd::CONFIG_DB_KEY_COA] as $dbKey) {
    $entityManager = \Wtd\Wtd::getEntityManager($dbKey, true);

    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
    $classes = $entityManager->getMetadataFactory()->getAllMetadata();
    $schemaTool->dropDatabase();
    $schemaTool->createSchema($classes);
}
