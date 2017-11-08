<?php

namespace DmServer;

use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Gedmo\Timestampable\TimestampableListener;
use JDesrosiers\Silex\Provider\CorsServiceProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class DmServer implements ControllerProviderInterface
{
    use RequestInterceptor;

    public static $isTestContext = false;

    const CONFIG_FILE_DEFAULT = 'config.db.ini';
    const CONFIG_FILE_TEST = 'config.db.test.ini';

    /** @var EntityManager[] $entityManagers */
    public static $entityManagers = [];

    const CONFIG_DB_KEY_DM = 'db_dm';
    const CONFIG_DB_KEY_COA = 'db_coa';
    const CONFIG_DB_KEY_COVER_ID = 'db_cover_id';
    const CONFIG_DB_KEY_DM_STATS = 'db_dm_stats';
    const CONFIG_DB_KEY_EDGECREATOR = 'db_edgecreator';

    public static $configuredEntityManagerNames = [self::CONFIG_DB_KEY_DM, self::CONFIG_DB_KEY_COA, self::CONFIG_DB_KEY_COVER_ID, self::CONFIG_DB_KEY_DM_STATS, self::CONFIG_DB_KEY_EDGECREATOR];

    public static $settings;

    public static function initSettings($fileName)
    {
        self::$settings = parse_ini_file(
            __DIR__.'/config/' . $fileName
            , true
        );
    }

    public function setup(Application $app)
    {
        $app['debug'] = true;
    }

    public static function getSchemas() {
        return parse_ini_file(
            __DIR__.'/config/schemas.ini'
            , true
        );
    }

    /**
     * @return array
     */
    public static function getAppConfig() {
        $config = parse_ini_file(
            __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . (self::$isTestContext ? self::CONFIG_FILE_TEST : self::CONFIG_FILE_DEFAULT)
            , true
        );
        $schemas = self::getSchemas();

        foreach($schemas as $dbKey => $genericConfigForDbKey) {
            if (array_key_exists($dbKey, $config)) {
                $config[$dbKey] = array_merge($config[$dbKey], $genericConfigForDbKey);
            }
            else {
                $config[$dbKey] = $genericConfigForDbKey;
            }
        }

        return $config;
    }

    public static function getAppRoles() {
        return parse_ini_file(
            __DIR__.(self::$isTestContext ? '/config/roles.base.ini' : '/config/roles.ini')
            , true
        );
    }

    /**
     * @param array $dbConf
     * @return array
     */
    public static function getConnectionParams($dbConf)
    {
        $username = $dbConf['username'];
        $password = $dbConf['password'];

        switch ($dbConf['type']) {
            case 'mysql':
                return [
                    'port' => $dbConf['port'],
                    'dbname' => $dbConf['dbname'],
                    'user' => $username,
                    'password' => $password,
                    'host' => $dbConf['host'],
                    'driver' => 'pdo_mysql',
                    'server_version' => '15.1',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ]
                ];
            break;

            case 'sqlite':
                $params = [
                    'user' => $username,
                    'password' => $password,
                    'driver' => 'pdo_sqlite'
                ];
                if (array_key_exists('in_memory', $dbConf)) {
                    $params['memory'] = true;
                }
                else {
                    $params['path'] = $dbConf['path'];
                }
                return $params;
            break;
        }
        return [];
    }

    /**
     * @param string $dbName
     * @return EntityManager|null
     */
    public static function getEntityManager($dbName) {
        if (!in_array($dbName, self::$configuredEntityManagerNames)) {
            return null;
        }
        else {
            if (!array_key_exists($dbName, self::$entityManagers)) {
                self::createEntityManager($dbName);
            }
            return self::$entityManagers[$dbName];
        }
    }

    /**
     * @param string $dbKey
     * @return EntityManager
     */
    public static function createEntityManager($dbKey)
    {
        $cache = new ArrayCache();
        // standard annotation reader
        $annotationReader = new AnnotationReader();
        $cachedAnnotationReader = new CachedReader(
            $annotationReader, // use reader
            $cache // and a cache driver
        );
        $evm = new EventManager();
        $timestampableListener = new TimestampableListener();
        $timestampableListener->setAnnotationReader($cachedAnnotationReader);
        $evm->addEventSubscriber($timestampableListener);

        $config = self::getAppConfig()[$dbKey];

        $metaDataConfig = Setup::createAnnotationMetadataConfiguration(
            [__DIR__ . "/models/".$config['models_path']], true, null, null, false
        );
        $connectionParams = self::getConnectionParams($config);
        $conn = DriverManager::getConnection($connectionParams, $metaDataConfig, $evm);
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('timestamp', 'integer');

        if (array_key_exists('tables', $config)) {
            $conn->getConfiguration()->setFilterSchemaAssetsExpression('~^'.$config['tables'].'$~');
        }

        $em = EntityManager::create($conn, $metaDataConfig);
        self::$entityManagers[$dbKey] = $em;

        return $em;
    }

    /**
     * Connect the controller classes to the routes
     * @param Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        // set up the service container
        $this->setup($app);

        // Load routes from the controller classes
        /** @var ControllerCollection $routing */
        $routing = $app['controllers_factory'];

        $app->register(new AnnotationServiceProvider(), [
            "annot.controllerDir" => __DIR__."/controllers"
        ]);

        $app->register(new CorsServiceProvider(), [
            "cors.allowOrigin" => "*",
        ]);
        $app["cors-enabled"]($routing);

        return $routing;
    }
}
