<?php
$loader = require __DIR__ . '/../vendor/autoload.php';

$entityManager = \Wtd\Wtd::getEntityManager(true);

$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
$classes = $entityManager->getMetadataFactory()->getAllMetadata();
$schemaTool->dropDatabase();
$schemaTool->createSchema($classes);
