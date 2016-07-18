<?php
namespace Wtd\Test;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Silex\Application;
use Silex\WebTestCase;
use Wtd\AppController;
use Wtd\Models\Numeros;
use Wtd\Wtd;

class TestCommon extends WebTestCase {

    protected static $conf;
    protected static $users;
    protected static $testUser = 'whattheduck';

    /** @var EntityManager $em  */
    protected static $em;

    /** @var Application $app */
    protected $app;

    public function setUp() {
        self::$conf = Wtd::getAppConfig(true);

        self::$em = Wtd::getEntityManager(true);
        $schemaTool = new SchemaTool(self::$em);
        $classes = self::$em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);

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

    protected function callService($path, $userCredentials, $parameters = array(), $systemCredentials = array()) {
        $client = static::createClient();
        $client->request('POST', $path, $userCredentials + $parameters, [], $systemCredentials);

        return $client->getResponse();
    }
    
    protected function callAuthenticatedService($path, $userCredentials, $parameters = array()) {
        return $this->callService($path, $userCredentials, $parameters, self::getDefaultSystemCredentials());
    }

    protected function getCurrentUserIssues() {
        return self::$em->getRepository(Numeros::class)->findBy(array('idUtilisateur' => AppController::getSessionUser($this->app)['id']));
    }
}