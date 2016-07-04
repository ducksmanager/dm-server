<?php
namespace Wtd\Test;

use Silex\Application;
use Silex\WebTestCase;

require __DIR__ . '/../test_bootstrap.php';

class CollectionTest extends WebTestCase
{
    private static $conf;
    private static $users;
    private static $testUser = 'whattheduck';

    public function setUp() {
        self::$conf = parse_ini_file(__DIR__.'/../../app/config/config.test.ini', true);
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

        require __DIR__ . '/../../index.php';

        self::$users = $users;
        return $app;
    }

    public function testCreateCollection() {
        $client = static::createClient();
        $client->request('POST', '/collection/new', [], [], [
            'PHP_AUTH_USER' => self::$testUser,
            'PHP_AUTH_PW'   => explode(':', self::$conf['user_roles'][self::$testUser])[1]
        ]);
    }
}
