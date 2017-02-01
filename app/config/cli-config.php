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
 * @return string
 */
function getCommandLineParameter($paramName) {
    $argvRegex = '#^--' . $paramName . '=(.+)$#';
    $argvMatch = preg_grep($argvRegex, $_SERVER['argv']);
    if (count($argvMatch) === 0) {
        return null;
    }
    return [
        array_values($argvMatch)[0],
        preg_replace(
            $argvRegex,
            '$1',
            array_values($argvMatch)[0]
        )
    ];
}

/**
 * @param string $paramName
 * @return string|null
 */
function getCommandLineParameterValue($paramName) {
    $commandLineParameter = getCommandLineParameter($paramName);
    return is_null($commandLineParameter) ? null : $commandLineParameter[1];
}

/**
 * @param string $parameterName
 */
function removeCommandLineParameter($parameterName)
{
    if (!is_null(getCommandLineParameterValue($parameterName))) {
        array_splice($_SERVER['argv'], array_search(getCommandLineParameter($parameterName)[0], $_SERVER['argv']), 1);
    }
}

/**
 * @return string|bool
 */
function getSchemaConfigKeyFromCommandLine()
{
    $schemaKeyFromNamespace = getSchemaConfigKey(getCommandLineParameterValue('namespace'));
    if ($schemaKeyFromNamespace !== false) {
        return $schemaKeyFromNamespace;
    }
    else {
        return getSchemaConfigKey(getCommandLineParameterValue('filter'));
    }
}

$schemaConfigKey = getSchemaConfigKeyFromCommandLine();

if ($schemaConfigKey === false) {
    echo "Namespace in command line not found among schemas : ".$_SERVER['argv']."\n";
}
else {
    if (!in_array('orm:generate-entities', $_SERVER['argv'])) {
        removeCommandLineParameter('filter');
    }
    if (!in_array('orm:convert-mapping', $_SERVER['argv'])) {
        removeCommandLineParameter('namespace');
    }
    $em = DmServer::createEntityManager($schemaConfigKey);
    return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
}