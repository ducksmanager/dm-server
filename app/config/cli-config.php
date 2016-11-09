<?php
require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/../Wtd.php";

use Wtd\Wtd;

function getSchemaConfigKey($modelNamespace) {
    $schemas = Wtd::getSchemas();

    return array_keys($schemas)[array_search($modelNamespace, array_column($schemas, 'models_namespace'))];
}

/**
 * @return bool
 */
function getSchemaConfigKeyFromCommandLine()
{
    $namespaceRegex = '#^--namespace=(.+)$#';
    $modelNamespace = preg_replace($namespaceRegex, '$1', array_values(preg_grep($namespaceRegex, $_SERVER['argv']))[0]);

    return getSchemaConfigKey($modelNamespace);
}

$em = Wtd::createEntityManager(getSchemaConfigKeyFromCommandLine());
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
