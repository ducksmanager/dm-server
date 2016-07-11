<?php
namespace Wtd\Test;

use Silex\Application;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

require __DIR__ . '/test_bootstrap.php';

class TestCommon extends WebTestCase {

    protected static $conf;
    protected static $users;
    protected static $testUser = 'whattheduck';

    public function setUp() {
        self::$conf = parse_ini_file(__DIR__.'/../app/config/config.test.ini', true);
        parent::setUp();
    }

    /**
     * @return Application
     */
    public function createApplication()
    {
        $app = null;
        $users = [];
        $conf = self::$conf;

        require __DIR__ . '/../index.php';

        self::$users = $users;
        return $app;
    }

    private static function getDefaultSystemCredentials() {
        return [
            'PHP_AUTH_USER' => self::$testUser,
            'PHP_AUTH_PW'   => explode(':', self::$conf['user_roles'][self::$testUser])[1]
        ];
    }

    protected function callService($userCredentials, $systemCredentials = array()) {
        $client = static::createClient();
        $client->request('POST', '/collection/new', $userCredentials, [], $systemCredentials);

        return $client->getResponse();
    }
    
    protected function callAuthenticatedService($userCredentials) {
        return $this->callService($userCredentials, self::getDefaultSystemCredentials());
    }
}