<?php

namespace DmServer;

use DmServer\Controllers;
use DmServer\Controllers\AbstractController;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Gedmo\Timestampable\TimestampableListener;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DmServer extends AbstractController implements ControllerProviderInterface
{
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
     * @param bool $forTest
     * @return array
     */
    public static function getAppConfig($forTest) {
        $config = parse_ini_file(
            __DIR__.'/config/' . ($forTest ? self::CONFIG_FILE_TEST : self::CONFIG_FILE_DEFAULT)
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

    public static function getAppRoles($forTest) {
        return parse_ini_file(
            __DIR__.($forTest ? '/config/roles.base.ini' : '/config/roles.ini')
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
                    'driver' => 'pdo_sqlite',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ]
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
     * @param bool $forTest
     * @return EntityManager|null
     */
    static function getEntityManager($dbName, $forTest = false) {
        if (!in_array($dbName, self::$configuredEntityManagerNames)) {
            echo 'Invalid entity manager : '.$dbName;
            return null;
        }
        else {
            if (!array_key_exists($dbName, self::$entityManagers)) {
                self::$entityManagers[$dbName] = self::createEntityManager($dbName, $forTest);
            }
            return self::$entityManagers[$dbName];
        }
    }

    /**
     * @param string $dbKey
     * @param bool $forTest
     * @return EntityManager
     */
    static function createEntityManager($dbKey, $forTest = false)
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

        $config = self::getAppConfig($forTest)[$dbKey];

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

        return EntityManager::create($conn, $metaDataConfig);
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

        $routing->before(/**
         * @param Request $request
         * @param Application $app
         * @return Response|void
         */
            function(Request $request, Application $app) {
                if (strpos($request->getPathInfo(), '/status') === 0) {
                    return;
                }
                if (is_null(self::getClientVersion($app))) {
                    $clientVersion = $request->headers->get('x-dm-version');
                    if (is_null($clientVersion)) {
                        return new Response('Unspecified client version', Response::HTTP_VERSION_NOT_SUPPORTED);
                    } else {
                        self::setClientVersion($app, $clientVersion);
                    }
                }

                $result = $this->authenticateUser($app, $request);
                if ($result instanceof Response) {
                    return $result;
                }
            }
        );

        Controllers\User\AppController::addRoutes($routing);
        Controllers\User\InternalController::addRoutes($routing);

        Controllers\Collection\AppController::addRoutes($routing);
        Controllers\Collection\InternalController::addRoutes($routing);

        Controllers\Coa\AppController::addRoutes($routing);
        Controllers\Coa\InternalController::addRoutes($routing);

        Controllers\CoverId\AppController::addRoutes($routing);
        Controllers\CoverId\InternalController::addRoutes($routing);

        Controllers\RawSql\AppController::addRoutes($routing);
        Controllers\RawSql\InternalController::addRoutes($routing);

        Controllers\Status\AppController::addRoutes($routing);

        Controllers\Stats\AppController::addRoutes($routing);
        Controllers\Stats\InternalController::addRoutes($routing);

        Controllers\EdgeCreator\AppController::addRoutes($routing);
        Controllers\EdgeCreator\InternalController::addRoutes($routing);

        return $routing;
    }
}
