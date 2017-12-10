<?php
require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/../DmServer.php";

use DmServer\DmServer;

trait CliConfig
{
    /**
     * @param string $modelNamespace
     * @return bool|string
     */
    public static function getSchemaConfigKey($modelNamespace)
    {
        if (is_null($modelNamespace)) {
            return false;
        }
        $schemas = DmServer::getSchemas();
        $modelNamespace = str_replace('\\', '', $modelNamespace);

        foreach ($schemas as $schemaKey => $schema) {
            if (str_replace('\\', '', $schema['models_namespace']) === $modelNamespace) {
                return $schemaKey;
            }
        }

        return false;
    }

    /**
     * @param string $paramName
     * @return array|null
     */
    public static function getCommandLineParameter($paramName)
    {
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
    public static function getCommandLineParameterValue($paramName)
    {
        $commandLineParameter = self::getCommandLineParameter($paramName);

        return is_null($commandLineParameter) ? null : $commandLineParameter[1];
    }

    /**
     * @param string $parameterName
     */
    public static function removeCommandLineParameter($parameterName)
    {
        if (!is_null(self::getCommandLineParameterValue($parameterName))) {
            array_splice($_SERVER['argv'], array_search(self::getCommandLineParameter($parameterName)[0], $_SERVER['argv']),
                1);
        }
    }

    /**
     * @return string|bool
     */
    public static function getSchemaConfigKeyFromCommandLine()
    {
        $schemaKeyFromNamespace = self::getSchemaConfigKey(self::getCommandLineParameterValue('namespace'));
        if ($schemaKeyFromNamespace !== false) {
            return $schemaKeyFromNamespace;
        } else {
            return self::getSchemaConfigKey(self::getCommandLineParameterValue('filter'));
        }
    }
}

$schemaConfigKey = CliConfig::getSchemaConfigKeyFromCommandLine();

if ($schemaConfigKey === false) {
    echo "Namespace in command line not found among schemas : ".$_SERVER['argv']."\n";
}
else {
    if (!in_array('orm:generate-entities', $_SERVER['argv'])) {
        CliConfig::removeCommandLineParameter('filter');
    }
    if (!in_array('orm:convert-mapping', $_SERVER['argv'])) {
        CliConfig::removeCommandLineParameter('namespace');
    }
    try {
        $em = DmServer::createEntityManager($schemaConfigKey);
        return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
    } catch (\Doctrine\DBAL\DBALException $e) {
        echo "Failed to retrieve Entity manager $schemaConfigKey";
    } catch (\Doctrine\ORM\ORMException $e) {
        echo "Failed to retrieve Entity manager $schemaConfigKey";
    }

}