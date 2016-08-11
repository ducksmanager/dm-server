<?php
namespace Wtd\Test;

use Coa\Models\InducksCountryname;
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

    /** @var array $modelClassesDm  */
    private static $modelClassesDm;

    /** @var array $modelClassesCoa  */
    private static $modelClassesCoa;

    /** @var SchemaTool $schemaToolDm  */
    private static $schemaToolDm;

    /** @var SchemaTool $schemaToolCoa  */
    private static $schemaToolCoa;

    /** @var Application $app */
    protected $app;

    public static function setUpBeforeClass()
    {
        self::$conf = Wtd::getAppConfig(true);

        $em = Wtd::getDmEntityManager(true);
        $coaEm = Wtd::getCoaEntityManager(true);

        self::$schemaToolDm = new SchemaTool($em);
        self::$modelClassesDm = $em->getMetadataFactory()->getAllMetadata();

        self::$schemaToolCoa = new SchemaTool($coaEm);
        self::$modelClassesCoa = $coaEm->getMetadataFactory()->getAllMetadata();
    }

    public function setUp() {
        self::initDatabase();
        parent::setUp();
    }

    protected static function initDatabase() {
        self::$schemaToolDm->dropDatabase();
        self::$schemaToolDm->createSchema(array_merge(
            self::$modelClassesDm,
            self::$modelClassesCoa
        ));
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
        return Wtd::$em->getRepository(Numeros::class)->findBy(array('idUtilisateur' => AppController::getSessionUser($this->app)['id']));
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
        Wtd::$em->persist($user);
        Wtd::$em->flush();

        $numero1 = new Numeros();
        $numero1->setPays('fr');
        $numero1->setMagazine('DDD');
        $numero1->setNumero('1');
        $numero1->setEtat('indefini');
        $numero1->setIdUtilisateur($user->getId());
        Wtd::$em->persist($numero1);

        $numero2 = new Numeros();
        $numero2->setPays('fr');
        $numero2->setMagazine('MP');
        $numero2->setNumero('300');
        $numero2->setEtat('bon');
        $numero2->setIdUtilisateur($user->getId());
        Wtd::$em->persist($numero2);

        $numero3 = new Numeros();
        $numero3->setPays('fr');
        $numero3->setMagazine('MP');
        $numero3->setNumero('301');
        $numero3->setEtat('mauvais');
        $numero3->setIdUtilisateur($user->getId());
        Wtd::$em->persist($numero3);

        Wtd::$em->flush();
    }

    protected static function createCoaData() {
        $country = new InducksCountryname();
        $country->setCountrycode('fr');
        $country->setLanguagecode('fr');
        $country->setCountryname('France');

        Wtd::$em->persist($country);

        Wtd::$em->flush();

    }
}