<?php

namespace Wtd;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;

class Wtd extends AppController implements ControllerProviderInterface
{
    const CONFIG_FILE_DEFAULT = 'config.ini';
    const CONFIG_FILE_TEST = 'config.test.ini';

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

        if (array_key_exists('dbname', $dbConf)) {
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
        } else {
            if (array_key_exists('path', $dbConf)) {
                return [
                    'user' => $username,
                    'password' => $password,
                    'path' =>  __DIR__ . '/../'.$dbConf['path'],
                    'driver' => 'pdo_sqlite',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ]
                ];
            }
        }
        return [];
    }

    /**
     * @param bool $forTest
     * @return EntityManager
     */
    static function getEntityManager($forTest = false)
    {
        $config = self::getAppConfig($forTest);

        $metaDataConfig = Setup::createAnnotationMetadataConfiguration(
            array(__DIR__ . "/models"), true, null, null, false
        );
        $connectionParams = self::getConnectionParams($config);
        $conn = DriverManager::getConnection($connectionParams, $metaDataConfig);
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('timestamp', 'integer');

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

        $routing->before(function($request, $app) {
            if (preg_match('#^/collection/((?!new/).)+$#', $request->getPathInfo())) {
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
        });

        CollectionController::addRoutes($routing);
        InternalController::addRoutes($routing);

        return $routing;
    }
}
