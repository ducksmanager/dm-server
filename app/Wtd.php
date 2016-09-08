<?php

namespace Wtd;

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

class Wtd extends AppController implements ControllerProviderInterface
{
    const CONFIG_FILE_DEFAULT = 'config.db.ini';
    const CONFIG_FILE_TEST = 'config.db.test.ini';

    const CONFIG_DB_KEY_DM = 'db';
    const CONFIG_DB_KEY_COA = 'db_coa';

    /** @var EntityManager $em */
    public static $em;

    /** @var EntityManager $coaEm */
    public static $coaEm;

    public function setup(Application $app)
    {
        $app['debug'] = true;
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
        $schemas = parse_ini_file(
            __DIR__.'/config/schemas.ini'
            , true
        );
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
            __DIR__.'/config/roles.ini'
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
                    'dbname' => $dbConf['dbname'],
                    'user' => $username,
                    'password' => $password,
                    'host' => 'localhost',
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

    static function getCoaEntityManager($forTest = false) {
        if (is_null(self::$coaEm)) {
            self::$coaEm = self::createEntityManager(self::CONFIG_DB_KEY_COA, $forTest);
        }
        return self::$coaEm;
    }

    static function getDmEntityManager($forTest = false) {
        if (is_null(self::$em)) {
            self::$em = self::createEntityManager(self::CONFIG_DB_KEY_DM, $forTest);
        }
        return self::$em;
    }

    /**
     * @param string $dbName
     * @param bool $forTest
     * @return EntityManager|null
     */
    static function getEntityManagerFromDbName($dbName, $forTest = false) {
        switch($dbName) {
            case self::CONFIG_DB_KEY_COA:
                return self::getCoaEntityManager($forTest);
                break;
            case self::CONFIG_DB_KEY_DM:
                return self::getDmEntityManager($forTest);
                break;
        }
        return null;
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
            array(__DIR__ . "/models/".$config['models_path']), true, null, null, false
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
            function($request, Application $app) {
                if (is_null(self::getClientVersion($app))) {
                    $clientVersion = $request->headers->get('x-wtd-version');
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

        CollectionController::addRoutes($routing);
        CoaListController::addRoutes($routing);
        InternalController::addRoutes($routing);
        RawSqlController::addRoutes($routing);

        return $routing;
    }
}
