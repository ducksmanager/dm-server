<?php
require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/../DmServer.php";

use DmServer\DmServer;

/**
 * @param string $modelNamespace
 * @return bool|string
 */
function getSchemaConfigKey($modelNamespace) {
    if (is_null($modelNamespace)) {
        return false;
    }
    $schemas = DmServer::getSchemas();
    $modelNamespace = str_replace('\\', '', $modelNamespace);

    foreach($schemas as $schemaKey=>$schema) {
        if (str_replace('\\', '', $schema['models_namespace']) === $modelNamespace) {
            return $schemaKey;
        }
    }
    return false;
}

/**
 * @param string $paramName
 * @return string|null
 */
function getCommandLineParameter($paramName) {
    $argvRegex = '#^--'.$paramName.'=(.+)$#';

    $argvMatch = preg_grep($argvRegex, $_SERVER['argv']);
    if (count($argvMatch) === 0) {
        return null;
    }
    else {
        return preg_replace(
            $argvRegex,
            '$1',
            array_values($argvMatch)[0]
        );
    }
}

/**
 * @return string|bool
 */
function getSchemaConfigKeyFromCommandLine()
{
    $schemaKeyFromNamespace = getSchemaConfigKey(getCommandLineParameter('namespace'));
    if ($schemaKeyFromNamespace !== false) {
        return $schemaKeyFromNamespace;
    }
    else {
        return getSchemaConfigKey(getCommandLineParameter('filter'));
    }
}

$schemaConfigKey = getSchemaConfigKeyFromCommandLine();
if ($schemaConfigKey === false) {
    echo "Namespace in command line not found among schemas : ".$_SERVER['argv']."\n";
}

$em = DmServer::createEntityManager($schemaConfigKey);
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
