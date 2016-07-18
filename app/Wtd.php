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
    const CONFIG_FILE_DEFAULT = 'config.ini';
    const CONFIG_FILE_TEST = 'config.test.ini';

    /** @var EntityManager $em */
    public static $em;

    public function setup(Application $app)
    {
        $app['debug'] = true;
    }

    /**
     * @param bool $forTest
     * @return array
     */
    public static function getAppConfig($forTest) {
        return parse_ini_file(
            __DIR__.'/config/' . ($forTest ? self::CONFIG_FILE_TEST : self::CONFIG_FILE_DEFAULT)
            , true
        );
    }

    /**
     * @param array $conf
     * @return array
     */
    public static function getConnectionParams($conf)
    {
        $dbConf = $conf['db'];

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
                return [
                    'user' => $username,
                    'password' => $password,
                    'memory' =>  $dbConf['in_memory'] === 'true',
                    'driver' => 'pdo_sqlite',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ]
                ];
            break;
        }
        return [];
    }

    /**
     * @param bool $forTest
     * @return EntityManager
     */
    static function getEntityManager($forTest = false)
    {
        if (is_null(self::$em)) {
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

            $config = self::getAppConfig($forTest);

            $metaDataConfig = Setup::createAnnotationMetadataConfiguration(
                array(__DIR__ . "/models"), true, null, null, false
            );
            $connectionParams = self::getConnectionParams($config);
            $conn = DriverManager::getConnection($connectionParams, $metaDataConfig, $evm);
            $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
            $conn->getDatabasePlatform()->registerDoctrineTypeMapping('timestamp', 'integer');

            self::$em = EntityManager::create($conn, $metaDataConfig);
        }
        return self::$em;
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
         * @return bool|Response
         */
            function($request, $app) {
                if (is_null(self::getClientVersion($app))) {
                    $clientVersion = $request->headers->get('x-wtd-version');
                    if (is_null($clientVersion)) {
                        return new Response('Unspecified client version', Response::HTTP_VERSION_NOT_SUPPORTED);
                    } else {
                        self::setClientVersion($app, $clientVersion);
                    }
                }

                if (preg_match('#^/collection/((?!new/?).)+$#', $request->getPathInfo())) {
                    $username = $request->request->get('username');
                    $password = $request->request->get('password');

                    $userCheck = self::callInternal($app, '/user/check', 'GET', [
                        'username' => $username,
                        'password' => $password
                    ]);
                    if ($userCheck->getStatusCode() !== Response::HTTP_OK) {
                        return $userCheck;
                    }
                    else {
                        $this->setSessionUser($app, $username, $userCheck->getContent());
                    }
                }
            }
        );

        CollectionController::addRoutes($routing);
        InternalController::addRoutes($routing);

        return $routing;
    }
}
