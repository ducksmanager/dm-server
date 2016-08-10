<?php
namespace Wtd\Test;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Silex\Application;
use Silex\WebTestCase;
use Wtd\AppController;
use Wtd\Models\Numeros;
use Wtd\Models\Users;
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
        self::$em = Wtd::getDmEntityManager(true);
        self::$schemaTool = new SchemaTool(self::$em);
        self::$modelClasses = self::$em->getMetadataFactory()->getAllMetadata();
    }

    public function setUp() {
        self::initDatabase();
        parent::setUp();
    }

    protected static function initDatabase() {
        self::$schemaTool->dropDatabase();
        self::$schemaTool->createSchema(self::$modelClasses);
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

    /**
     * @param string $path
     * @param array $userCredentials
     * @param array $parameters
     * @param array $systemCredentials
     * @param string $method
     * @return TestServiceCallCommon
     */
    protected function buildService(
        $path,
        $userCredentials,
        $parameters = array(),
        $systemCredentials = array(),
        $method = 'POST'
    ) {
        $service = new TestServiceCallCommon(static::createClient());
        $service->setPath($path);
        $service->setUserCredentials($userCredentials);
        $service->setParameters($parameters);
        $service->setSystemCredentials($systemCredentials);
        $service->setMethod($method);
        return $service;
    }
    
    protected function buildAuthenticatedService($path, $userCredentials, $parameters = array()) {
        return $this->buildService($path, $userCredentials, $parameters, self::getDefaultSystemCredentials(), 'POST');
    }

    protected function buildAuthenticatedServiceWithTestUser($path, $method, $parameters = array()) {
        return $this->buildService(
            $path, [
            'username' => 'dm_user',
            'password' => 'dm_pass'
        ], $parameters, self::getDefaultSystemCredentials(), $method
        );
    }

    protected function getCurrentUserIssues() {
        return self::$em->getRepository(Numeros::class)->findBy(array('idUtilisateur' => AppController::getSessionUser($this->app)['id']));
    }

    /**
     * @param string $username
     */
    protected static function createTestCollection($username = 'dm_user') {
        $user = new Users();
        $user->setUsername($username);
        $user->setPassword(sha1('dm_pass'));
        $user->setEmail('test@ducksmanager.net');
        $user->setDateinscription(\DateTime::createFromFormat('Y-m-d', '2000-01-01'));
        self::$em->persist($user);
        self::$em->flush();

        $numero1 = new Numeros();
        $numero1->setPays('fr');
        $numero1->setMagazine('DDD');
        $numero1->setNumero('1');
        $numero1->setEtat('indefini');
        $numero1->setIdUtilisateur($user->getId());
        self::$em->persist($numero1);

        $numero2 = new Numeros();
        $numero2->setPays('fr');
        $numero2->setMagazine('MP');
        $numero2->setNumero('300');
        $numero2->setEtat('bon');
        $numero2->setIdUtilisateur($user->getId());
        self::$em->persist($numero2);

        $numero3 = new Numeros();
        $numero3->setPays('fr');
        $numero3->setMagazine('MP');
        $numero3->setNumero('301');
        $numero3->setEtat('mauvais');
        $numero3->setIdUtilisateur($user->getId());
        self::$em->persist($numero3);

        self::$em->flush();
    }
}