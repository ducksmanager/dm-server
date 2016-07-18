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

    /** @var array $modelClasses  */
    private static $modelClasses;

    /** @var SchemaTool $schemaTool  */
    private static $schemaTool;

    /** @var Application $app */
    protected $app;

    public static function setUpBeforeClass()
    {
        self::$conf = Wtd::getAppConfig(true);
        self::$em = Wtd::getEntityManager(true);
        self::$schemaTool = new SchemaTool(self::$em);
        self::$modelClasses = self::$em->getMetadataFactory()->getAllMetadata();
    }

    public function setUp() {
        self::$schemaTool->dropDatabase();
        self::$schemaTool->createSchema(self::$modelClasses);

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

    private static function getDefaultSystemCredentials($version='1.3+') {
        return self::getDefaultSystemCredentialsNoVersion() + [
            'HTTP_X_WTD_VERSION' => $version
        ];
    }

    protected static function getDefaultSystemCredentialsNoVersion() {
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

    protected function callAuthenticatedServiceWithTestUser($path, $parameters = array()) {
        return $this->callService(
            $path, [
                'username' => 'dm_user',
                'password' => 'dm_pass'
            ],
            $parameters,
            self::getDefaultSystemCredentials()
        );
    }

    protected function getCurrentUserIssues() {
        return self::$em->getRepository(Numeros::class)->findBy(array('idUtilisateur' => AppController::getSessionUser($this->app)['id']));
    }
}