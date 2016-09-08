<?php
require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/../Wtd.php";

use Wtd\Wtd;

/**
 * @return bool
 */
function isCoaConfig()
{
    $namespaceRegex = '#^--namespace=(.+)$#';
    $modelNamespace = preg_replace($namespaceRegex, '$1', array_values(preg_grep($namespaceRegex, $_SERVER['argv']))[0]);

    return $modelNamespace === 'Coa\\Models\\';
}

$em = Wtd::createEntityManager(isCoaConfig() ? Wtd::CONFIG_DB_KEY_COA : Wtd::CONFIG_DB_KEY_DM);
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
