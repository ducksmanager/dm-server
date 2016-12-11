<?php
namespace Wtd\Test;

use Coa\Models\InducksCountryname;
use Coa\Models\InducksIssue;
use Coa\Models\InducksPublication;
use CoverId\Models\Covers;
use Silex\Application;
use Silex\WebTestCase;
use Wtd\AppController;
use Wtd\Models\Numeros;
use Wtd\Models\Users;
use Wtd\Wtd;

class TestCommon extends WebTestCase {

    protected static $conf;
    protected static $roles;
    protected static $users;
    protected static $testUser = 'whattheduck';
    protected static $rawSqlUser = 'rawsql';
    protected static $uploadBase = '/tmp/dm-server';

    protected static $exampleImage = 'cover_example.jpg';

    /** @var SchemaWithClasses[] $schemas */
    private static $schemas = [];

    /** @var Application $app */
    protected $app;

    public static function setUpBeforeClass()
    {
        Wtd::initSettings('settings.test.ini');
        self::$conf = Wtd::getAppConfig(true);
        self::$roles = Wtd::getAppRoles();

        foreach(Wtd::$configuredEntityManagerNames as $emName) {
            self::$schemas[$emName] = SchemaWithClasses::createFromEntityManager(Wtd::getEntityManager($emName, true));
        }
    }

    public function setUp() {
        self::initDatabase();
        @rmdir(Wtd::$settings['image_local_root']);
        @mkdir(Wtd::$settings['image_local_root'], 0777, true);
        parent::setUp();
    }

    protected static function initDatabase() {

        foreach(Wtd::$configuredEntityManagerNames as $emName) {
            self::$schemas[$emName]->recreateSchema();
        }
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

    private static function getSystemCredentials($appUser, $version = '1.3+') {
        return self::getSystemCredentialsNoVersion($appUser) + [
            'HTTP_X_WTD_VERSION' => $version
        ];
    }

    protected static function getSystemCredentialsNoVersion($appUser) {
        return [
            'PHP_AUTH_USER' => $appUser,
            'PHP_AUTH_PW'   => explode(':', self::$roles[$appUser])[1]
        ];
    }

    /**
     * @param string $path
     * @param array $userCredentials
     * @param array $parameters
     * @param array $systemCredentials
     * @param string $method
     * @param array $files
     * @return TestServiceCallCommon
     */
    protected function buildService(
        $path, $userCredentials, $parameters = array(), $systemCredentials = array(), $method = 'POST', $files = array()
    ) {
        $service = new TestServiceCallCommon(static::createClient());
        $service->setPath($path);
        $service->setUserCredentials($userCredentials);
        $service->setParameters($parameters);
        $service->setSystemCredentials($systemCredentials);
        $service->setMethod($method);
        $service->setFiles($files);
        return $service;
    }
    
    protected function buildAuthenticatedService($path, $appUser, $userCredentials, $parameters = array(), $files = array()) {
        return $this->buildService($path, $userCredentials, $parameters, self::getSystemCredentials($appUser), 'POST', $files);
    }

    protected function buildAuthenticatedServiceWithTestUser($path, $appUser, $method, $parameters = array(), $files = array()) {
        return $this->buildService(
            $path, [
            'username' => 'dm_user',
            'password' => 'dm_pass'
        ], $parameters, self::getSystemCredentials($appUser), $method, $files
        );
    }

    protected function getCurrentUserIssues() {
        $dmEntityManager = Wtd::$entityManagers[Wtd::CONFIG_DB_KEY_DM];
        return $dmEntityManager->getRepository(Numeros::class)->findBy(
            array('idUtilisateur' => AppController::getSessionUser($this->app)['id'])
        );
    }

    /**
     * @param string $username
     */
    protected static function createTestCollection($username = 'dm_user') {
        $dmEntityManager = Wtd::$entityManagers[Wtd::CONFIG_DB_KEY_DM];

        $user = new Users();
        $user->setUsername($username);
        $user->setPassword(sha1('dm_pass'));
        $user->setEmail('test@ducksmanager.net');
        $user->setDateinscription(\DateTime::createFromFormat('Y-m-d', '2000-01-01'));
        $dmEntityManager->persist($user);

        $dmEntityManager->flush();

        $numero1 = new Numeros();
        $numero1->setPays('fr');
        $numero1->setMagazine('DDD');
        $numero1->setNumero('1');
        $numero1->setEtat('indefini');
        $numero1->setIdUtilisateur($user->getId());
        $dmEntityManager->persist($numero1);
        $dmEntityManager->flush();

        $numero2 = new Numeros();
        $numero2->setPays('fr');
        $numero2->setMagazine('MP');
        $numero2->setNumero('300');
        $numero2->setEtat('bon');
        $numero2->setIdUtilisateur($user->getId());
        $dmEntityManager->persist($numero2);
        $dmEntityManager->flush();

        $numero3 = new Numeros();
        $numero3->setPays('fr');
        $numero3->setMagazine('MP');
        $numero3->setNumero('301');
        $numero3->setEtat('mauvais');
        $numero3->setIdUtilisateur($user->getId());
        $dmEntityManager->persist($numero3);

        $dmEntityManager->flush();
    }

    protected static function createCoaData() {
        $coaEntityManager = Wtd::$entityManagers[Wtd::CONFIG_DB_KEY_COA];
        
        $country1 = new InducksCountryname();
        $country1->setCountrycode('fr');
        $country1->setLanguagecode('fr');
        $country1->setCountryname('France');
        $coaEntityManager->persist($country1);

        $country2 = new InducksCountryname();
        $country2->setCountrycode('es');
        $country2->setLanguagecode('fr');
        $country2->setCountryname('Espagne');
        $coaEntityManager->persist($country2);

        $publication1 = new InducksPublication();
        $publication1->setPublicationCode('fr/DDD');
        $publication1->setCountrycode('fr');
        $publication1->setTitle('Dynastie');
        $coaEntityManager->persist($publication1);

        $publication2 = new InducksPublication();
        $publication2->setPublicationCode('fr/MP');
        $publication2->setCountrycode('fr');
        $publication2->setTitle('Parade');
        $coaEntityManager->persist($publication2);

        $issue1 = new InducksIssue();
        $issue1->setPublicationcode('fr/DDD');
        $issue1->setIssuenumber('1');
        $issue1->setIssuecode('fr/DDD 1');
        $coaEntityManager->persist($issue1);

        $issue2 = new InducksIssue();
        $issue2->setPublicationcode('fr/DDD');
        $issue2->setIssuenumber('2');
        $issue2->setIssuecode('fr/DDD 2');
        $coaEntityManager->persist($issue2);

        $issue3 = new InducksIssue();
        $issue3->setPublicationcode('fr/MP');
        $issue3->setIssuenumber('300');
        $issue3->setIssuecode('fr/MP 300');
        $coaEntityManager->persist($issue3);

        $coaEntityManager->flush();
    }

    protected static function createCoverIds()
    {
        $coverIdEntityManager = Wtd::$entityManagers[Wtd::CONFIG_DB_KEY_COVER_ID];

        $coverIds = [];

        $urls = [
            'fr/DDD 1' => 'webusers/2010/12/fr_ddd_001a_001.jpg',
            'fr/DDD 2' => 'webusers/2010/12/fr_ddd_002a_001.jpg',
            'fr/MP 300' => 'webusers/2010/12/fr_mp_0300a_001.jpg'
        ];

        foreach($urls as $issueNumber => $url) {
            $cover = new Covers();
            $cover->setIssuecode($issueNumber);
            $cover->setUrl($url);
            $coverIdEntityManager->persist($cover);
            $coverIdEntityManager->flush();
            $coverIds[]= $cover->getId();

            @mkdir(Wtd::$settings['image_remote_root'].dirname($url), 0777, true);
            $imagePath = self::getPathToFileToUpload(self::$exampleImage);
            file_put_contents(Wtd::$settings['image_remote_root'] . $url, file_get_contents($imagePath));
        }

        return $coverIds;
    }

    protected static function getPathToFileToUpload($fileName) {
        return implode(DIRECTORY_SEPARATOR, array(__DIR__, 'fixtures', $fileName));
    }
}