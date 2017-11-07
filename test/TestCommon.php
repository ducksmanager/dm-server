<?php
namespace DmServer\Test;

use Coa\Models\InducksCountryname;
use Coa\Models\InducksEntry;
use Coa\Models\InducksEntryurl;
use Coa\Models\InducksIssue;
use Coa\Models\InducksPerson;
use Coa\Models\InducksPublication;
use Coa\Models\InducksStory;
use Coa\Models\InducksStoryversion;
use Coverid\Models\Covers;
use Dm\Models\Achats;
use Dm\Models\BibliothequeOrdreMagazines;
use Dm\Models\Numeros;
use Dm\Models\TranchesDoublons;
use Dm\Models\TranchesPretes;
use Dm\Models\Users;
use DmServer\DmServer;
use DmServer\RequestUtil;
use Stats\Models\AuteursHistoires;
use Stats\Models\AuteursPseudosSimple;
use Stats\Models\UtilisateursHistoiresManquantes;
use Stats\Models\UtilisateursPublicationsManquantes;
use Stats\Models\UtilisateursPublicationsSuggerees;
use Edgecreator\Models\EdgecreatorIntervalles;
use Edgecreator\Models\EdgecreatorModeles2;
use Edgecreator\Models\EdgecreatorValeurs;
use Edgecreator\Models\ImagesTranches;
use Edgecreator\Models\TranchesEnCoursContributeurs;
use Edgecreator\Models\TranchesEnCoursModeles;
use Edgecreator\Models\TranchesEnCoursModelesImages;
use Edgecreator\Models\TranchesEnCoursValeurs;
use Silex\Application;
use Silex\WebTestCase;

class TestCommon extends WebTestCase {

    use RequestUtil;

    protected static $conf;
    protected static $roles;
    protected static $users;
    protected static $defaultTestDmUserName = 'dm_test_user';
    protected static $testDmUsers = [
        'dm_test_user' => 'test'
    ];
    protected static $dmUser = 'ducksmanager';
    protected static $edgecreatorUser = 'edgecreator';
    protected static $rawSqlUser = 'rawsql';
    protected static $adminUser = 'admin';
    protected static $uploadBase = '/tmp/dm-server';

    protected static $exampleImage = 'cover_example.jpg';

    /** @var SchemaWithClasses[] $schemas */
    private static $schemas = [];

    /** @var Application $app */
    protected $app;

    // Test data

    /** @var InducksCountryname[] $testCountries */
    private static $testCountries = [];

    /** @var InducksPublication[] $testPublications */
    private static $testPublications = [];

    /** @var InducksIssue[] $testIssues */
    private static $testIssues = [];

    /** @var InducksStory[] $testStories */
    private static $testStories = [];

    /** @var InducksStoryversion[] $testStoryversions */
    private static $testStoryversions = [];

    /** @var InducksEntry[] $testEntries */
    private static $testEntries = [];

    /** @var InducksEntryurl[] $testEntryurls */
    private static $testEntryurls = [];

    // Test data - end

    public static function setUpBeforeClass()
    {
        DmServer::initSettings('settings.test.ini');
        self::$conf = DmServer::getAppConfig();
        self::$roles = DmServer::getAppRoles();
    }

    public static function createEntityManagers()
    {
        DmServer::$entityManagers = [];

        foreach (DmServer::$configuredEntityManagerNames as $emName) {
            self::recreateSchema($emName);
        }
    }

    public function setUp() {
        self::initDatabase();
        @rmdir(DmServer::$settings['image_local_root']);
        @mkdir(DmServer::$settings['image_local_root'], 0777, true);
        parent::setUp();
    }

    public function tearDown()
    {
        foreach(DmServer::$configuredEntityManagerNames as $emName) {
            if (! DmServer::getEntityManager($emName)->isOpen()) {
                unset(DmServer::$entityManagers[$emName]);
                self::recreateSchema($emName);
            }
        }
        parent::tearDown();
    }

    protected static function initDatabase() {

        foreach(DmServer::$configuredEntityManagerNames as $emName) {
            self::$schemas[$emName]->recreateSchema();
            DmServer::getEntityManager($emName)->clear();
        }
    }

    protected static function recreateSchema($emName) {
        self::$schemas[$emName] = SchemaWithClasses::createFromEntityManager(DmServer::getEntityManager($emName));
    }

    /**
     * @return Application
     */
    public function createApplication()
    {
        $app = null;
        $users = [];
        /** @noinspection PhpUnusedLocalVariableInspection */
        $conf = self::$conf;

        require __DIR__ . '/../index.php';

        self::$users = $users;
        $app['session.test'] = true;
        return $app;
    }

    private static function getSystemCredentials($appUser, $version = '1.3+') {
        return self::getSystemCredentialsNoVersion($appUser) + [
            'HTTP_X_DM_VERSION' => $version
        ];
    }

    protected static function getSystemCredentialsNoVersion($appUser) {
        return [
            'HTTP_AUTHORIZATION' => 'Basic '.base64_encode(implode(':', [$appUser, explode(':', self::$roles[$appUser])[1]]))
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
        $path, $userCredentials, $parameters = [], $systemCredentials = [], $method = 'POST', $files = []
    ) {
        $service = new TestServiceCallCommon($this->createClient());
        $service->setPath($path);
        $service->setUserCredentials($userCredentials);
        $service->setParameters($parameters);
        $service->setSystemCredentials($systemCredentials);
        $service->setMethod($method);
        $service->setFiles($files);
        return $service;
    }
    
    protected function buildAuthenticatedService($path, $appUser, $userCredentials, $parameters = [], $method = 'POST') {
        return $this->buildService($path, $userCredentials, $parameters, self::getSystemCredentials($appUser), $method, []);
    }

    protected function buildAuthenticatedServiceWithTestUser($path, $appUser, $method = 'GET', $parameters = [], $files = []) {
        return $this->buildService(
            $path, [
            'username' => self::$defaultTestDmUserName,
            'password' => sha1(self::$testDmUsers[self::$defaultTestDmUserName])
        ], $parameters, self::getSystemCredentials($appUser), $method, $files
        );
    }

    protected function getCurrentUserIssues() {
        $dmEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_DM];
        return $dmEntityManager->getRepository(Numeros::class)->findBy(
            ['idUtilisateur' => self::getSessionUser($this->app)['id']]
        );
    }

    protected static function createUser($username, $password) {
        $dmEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_DM];

        $user = new Users();
        $dmEntityManager->persist(
            $user
                ->setBetauser(false)
                ->setUsername($username)
                ->setPassword(sha1($password))
                ->setEmail('test@ducksmanager.net')
                ->setDateinscription(\DateTime::createFromFormat('Y-m-d', '2000-01-01'))
        );

        $dmEntityManager->flush();

        return $user;
    }

    /**
     * @param string $username
     * @return array user info
     */
    protected static function createTestCollection($username = 'dm_test_user') {
        $dmEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_DM];

        $user = self::createUser($username, self::$testDmUsers[$username] ?? 'password');

        $numero1 = new Numeros();
        $dmEntityManager->persist(
            $numero1
                ->setPays('fr')
                ->setMagazine('DDD')
                ->setNumero('1')
                ->setEtat('indefini')
                ->setIdAcquisition('-2')
                ->setAv(false)
                ->setIdUtilisateur($user->getId())
        );

        $numero2 = new Numeros();
        $dmEntityManager->persist(
            $numero2
                ->setPays('fr')
                ->setMagazine('MP')
                ->setNumero('300')
                ->setEtat('bon')
                ->setIdUtilisateur($user->getId())
        );

        $numero3 = new Numeros();
        $dmEntityManager->persist(
            $numero3
                ->setPays('fr')
                ->setMagazine('MP')
                ->setNumero('301')
                ->setEtat('mauvais')
                ->setIdUtilisateur($user->getId())
        );

        $purchase1 = new Achats();
        $dmEntityManager->persist(
            $purchase1
                ->setDate(\DateTime::createFromFormat('Y-m-d', '2010-01-01'))
                ->setDescription('Purchase')
                ->setIdUser($user->getId())
        );

        $publicationSort1 = new BibliothequeOrdreMagazines();
        $dmEntityManager->persist(
            $publicationSort1
                ->setPays('fr')
                ->setMagazine('DDD')
                ->setIdUtilisateur($user->getId())
                ->setOrdre(1)
        );

        $publicationSort2 = new BibliothequeOrdreMagazines();
        $dmEntityManager->persist(
            $publicationSort2
                ->setPays('fr')
                ->setMagazine('JM')
                ->setIdUtilisateur($user->getId())
                ->setOrdre(2)
        );

        $dmEntityManager->flush();
        $dmEntityManager->clear();

        return ['username' => $user->getUsername(), 'id' => $user->getId(), 'purchaseIds' => [$purchase1->getIdAcquisition()] ];
    }

    protected static function createCoaData() {
        $coaEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COA];
        
        self::$testCountries['fr'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['fr']
                ->setCountrycode('fr')
                ->setLanguagecode('fr')
                ->setCountryname('France')
        );

        self::$testCountries['es'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['es']
                ->setCountrycode('es')
                ->setLanguagecode('fr')
                ->setCountryname('Espagne')
        );

        self::$testCountries['us'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['us']
                ->setCountrycode('us')
                ->setLanguagecode('fr')
                ->setCountryname('USA')
        );

        self::$testPublications['fr/DDD'] = new InducksPublication();
        $coaEntityManager->persist(
            self::$testPublications['fr/DDD']
                ->setPublicationCode('fr/DDD')
                ->setCountrycode('fr')
                ->setTitle('Dynastie')
        );

        self::$testPublications['fr/MP'] = new InducksPublication();
        $coaEntityManager->persist(
            self::$testPublications['fr/MP']
                ->setPublicationCode('fr/MP')
                ->setCountrycode('fr')
                ->setTitle('Parade')
        );

        self::$testPublications['us/CBL'] = new InducksPublication();
        $coaEntityManager->persist(
            self::$testPublications['us/CBL']
                ->setPublicationCode('us/CBL')
                ->setCountrycode('us')
                ->setTitle('Carl Barks Library')
        );

        self::$testIssues['fr/DDD 1'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/DDD 1']
                ->setPublicationcode('fr/DDD')
                ->setIssuenumber('1')
                ->setIssuecode('fr/DDD 1')
        );

        self::$testIssues['fr/DDD 2'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/DDD 2']
                ->setPublicationcode('fr/DDD')
                ->setIssuenumber('2')
                ->setIssuecode('fr/DDD 2')
        );

        self::$testIssues['fr/MP 300'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/MP 300']
                ->setPublicationcode('fr/MP')
                ->setIssuenumber('300')
                ->setIssuecode('fr/MP 300')
        );

        self::$testIssues['fr/PM 315'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/PM 315']
                ->setPublicationcode('fr/PM')
                ->setIssuenumber('315')
                ->setIssuecode('fr/PM 315')
        );

        self::$testIssues['us/CBL 7'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['us/CBL 7']
                ->setPublicationcode('us/CBL')
                ->setIssuenumber('7')
                ->setIssuecode('us/CBL 7')
        );

        self::$testStories['W WDC  31-05'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['W WDC  31-05']
                ->setTitle('Title of story W WDC  31-05')
                ->setStorycomment('Comment of story W WDC  31-05')
                ->setStorycode('W WDC  31-05')
        );

        self::$testStories['W WDC  32-02'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['W WDC  32-02']
                ->setTitle('Title of story W WDC  32-02')
                ->setStorycomment('Comment of story W WDC  32-02')
                ->setStorycode('W WDC  32-02')
        );

        self::$testStories['ARC CBL 5B'] = new InducksStory();
        $coaEntityManager->persist(
        self::$testStories['ARC CBL 5B']
            ->setTitle('Title of story ARC CBL 5B')
            ->setStorycomment('Comment of story ARC CBL 5B')
            ->setStorycode('ARC CBL 5B')
        );

        self::$testStories['W WDC 130-02'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['W WDC 130-02']
                ->setTitle('Title of story W WDC 130-02')
                ->setStorycomment('Comment of story W WDC 130-02')
                ->setStorycode('W WDC 130-02')
        );

        self::$testStories['AR 201'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['AR 201']
                ->setTitle('Title of story AR 201')
                ->setStorycomment('Comment of story AR 201')
                ->setStorycode('AR 201')
        );

        self::$testStoryversions['W WDC  31-05'] = new InducksStoryversion();
        $coaEntityManager->persist(
            self::$testStoryversions['W WDC  31-05']
                ->setStorycode('W WDC  31-05')
        );

        self::$testStoryversions['de/SPBL 136c'] = new InducksStoryversion();
        $coaEntityManager->persist(
            self::$testStoryversions['W WDC  31-05']
                ->setStorycode('W WDC  31-05')
        );

        self::$testEntries['us/CBL 7a'] = new InducksEntry();
        $coaEntityManager->persist(
            self::$testEntries['us/CBL 7a']
                ->setEntrycode('us/CBL 7a')
                ->setIssuecode('fr/DDD 1')
                ->setStoryversioncode('W WDC  31-05')
        );

        self::$testEntryurls['us/CBL 7p000a'] = new InducksEntryurl();
        $coaEntityManager->persist(
            self::$testEntryurls['us/CBL 7p000a']
                ->setEntrycode('us/CBL 7p000a')
                ->setUrl('us/cbl/us_cbl_7p000a_001.png')
                ->setSitecode('thumbnails')
        );

        $inducksPerson = new InducksPerson();
        $coaEntityManager->persist(
            $inducksPerson
                ->setPersoncode("CB")
                ->setFullname("Carl Barks")
        );

        $inducksPerson = new InducksPerson();
        $coaEntityManager->persist(
            $inducksPerson->setPersoncode("DR")
                ->setFullname("Don Rosa")
        );

        $coaEntityManager->flush();
        $coaEntityManager->clear();
    }

    public function createStatsData() {
        $dmStatsEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_DM_STATS];

        $userId = self::getSessionUser($this->app)['id'];

        // Author 1

        $authorUser1 = new AuteursPseudosSimple();
        $dmStatsEntityManager->persist(
            $authorUser1
                ->setIdUser($userId)
                ->setNomauteurabrege('CB')
                ->setNotation(2)
        );

        $author1Story1 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author1Story1
                ->setPersoncode('CB')
                ->setStorycode(self::$testStories['ARC CBL 5B']->getStorycode())
        ); // Missing, 1 issue suggested

        $author1Story2 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author1Story2
                ->setPersoncode('CB')
                ->setStorycode(self::$testStories['W WDC  32-02']->getStorycode())
        ); // Missing, 2 issue suggested (the same as story 1 + another one)

        $author1Story3 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author1Story3
                ->setPersoncode('CB')
                ->setStorycode(self::$testStories['W WDC  31-05']->getStorycode())
        ); // Not missing for user

        $author1Story4 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author1Story4
                ->setPersoncode('CB')
                ->setStorycode(self::$testStories['W WDC 130-02']->getStorycode())
        ); // Missing, 2 issues suggested

        $missingAuthor1Story1ForUser = new UtilisateursHistoiresManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Story1ForUser
                ->setPersoncode($author1Story1->getPersoncode())
                ->setStorycode($author1Story1->getStorycode())
                ->setIdUser($userId)
        );

        $missingAuthor1Story2ForUser = new UtilisateursHistoiresManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Story2ForUser
                ->setPersoncode($author1Story2->getPersoncode())
                ->setStorycode($author1Story2->getStorycode())
                ->setIdUser($userId)
        );

        $missingAuthor1Story4ForUser = new UtilisateursHistoiresManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Story4ForUser
                ->setPersoncode($author1Story4->getPersoncode())
                ->setStorycode($author1Story4->getStorycode())
                ->setIdUser($userId)
        );

        $missingAuthor1Issue1Story1ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Issue1Story1ForUser
                ->setPersoncode($author1Story1->getPersoncode())
                ->setStorycode($author1Story1->getStorycode())
                ->setIdUser($userId)
                ->setPublicationcode(self::$testIssues['us/CBL 7']->getPublicationcode())
                ->setIssuenumber(self::$testIssues['us/CBL 7']->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $missingAuthor1Issue1Story2ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Issue1Story2ForUser->setPersoncode($author1Story2->getPersoncode())
            ->setStorycode($author1Story2->getStorycode())
            ->setIdUser($userId)
            ->setPublicationcode(self::$testIssues['us/CBL 7']->getPublicationcode())
            ->setIssuenumber(self::$testIssues['us/CBL 7']->getIssuenumber())
            ->setNotation($authorUser1->getNotation())
        );

        $missingAuthor1Issue2Story2ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Issue2Story2ForUser->setPersoncode($author1Story2->getPersoncode())
                ->setStorycode($author1Story2->getStorycode())
                ->setIdUser($userId)
                ->setPublicationcode(self::$testIssues['fr/DDD 1']->getPublicationcode())
                ->setIssuenumber(self::$testIssues['fr/DDD 1']->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $missingAuthor1Issue1Story4ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Issue1Story4ForUser->setPersoncode($author1Story4->getPersoncode())
                ->setStorycode($author1Story4->getStorycode())
                ->setIdUser($userId)
                ->setPublicationcode(self::$testIssues['fr/PM 315']->getPublicationcode())
                ->setIssuenumber(self::$testIssues['fr/PM 315']->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $dmStatsEntityManager->flush();

        // Author 2

        $authorUser2 = new AuteursPseudosSimple();
        $dmStatsEntityManager->persist(
            $authorUser2
                ->setIdUser($userId)
                ->setNomauteurabrege('DR')
                ->setNotation(4)
        );

        $author2Story5 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author2Story5
                ->setPersoncode('DR')
                ->setStorycode(self::$testStories['AR 201']->getStorycode())
        );  // Missing, 1 issue suggested

        $missingAuthor2Story1ForUser = new UtilisateursHistoiresManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor2Story1ForUser
                ->setPersoncode($author2Story5->getPersoncode())
                ->setStorycode($author2Story5->getStorycode())
                ->setIdUser($userId)
        );

        $missingAuthor2Issue5Story5ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor2Issue5Story5ForUser
                ->setPersoncode($author2Story5->getPersoncode())
                ->setStorycode($author2Story5->getStorycode())
                ->setIdUser($userId)
                ->setPublicationcode(self::$testIssues['fr/PM 315']->getPublicationcode())
                ->setIssuenumber(self::$testIssues['fr/PM 315']->getIssuenumber())
                ->setNotation($authorUser2->getNotation())
        );


        // Suggested issues

        $suggestedIssue1ForUser = new UtilisateursPublicationsSuggerees();
        $dmStatsEntityManager->persist(
            $suggestedIssue1ForUser
                ->setPublicationcode(self::$testIssues['us/CBL 7']->getPublicationcode())
                ->setIssuenumber(self::$testIssues['us/CBL 7']->getIssuenumber())
                ->setIdUser($userId)
                ->setScore($missingAuthor1Issue1Story2ForUser->getNotation() + $missingAuthor1Issue1Story2ForUser->getNotation())
        );

        $suggestedIssue2ForUser = new UtilisateursPublicationsSuggerees();
        $dmStatsEntityManager->persist(
            $suggestedIssue2ForUser
                ->setPublicationcode(self::$testIssues['fr/DDD 1']->getPublicationcode())
                ->setIssuenumber(self::$testIssues['fr/DDD 1']->getIssuenumber())
                ->setIdUser($userId)
                ->setScore($missingAuthor1Issue2Story2ForUser->getNotation())
        );

        $suggestedIssue3ForUser = new UtilisateursPublicationsSuggerees();
        $dmStatsEntityManager->persist(
            $suggestedIssue3ForUser
                ->setPublicationcode(self::$testIssues['fr/PM 315']->getPublicationcode())
                ->setIssuenumber(self::$testIssues['fr/PM 315']->getIssuenumber())
                ->setIdUser($userId)
                ->setScore($missingAuthor1Issue1Story4ForUser->getNotation() + $missingAuthor2Issue5Story5ForUser->getNotation())
        );

        $dmStatsEntityManager->flush();
        $dmStatsEntityManager->clear();
    }

    public function createEdgeCreatorData()
    {
        $edgeCreatorEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_EDGECREATOR];

        /** @var Users $edgeCreatorUser */
        $edgeCreatorUser = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_DM]->getRepository(Users::class)->find(
            self::getSessionUser($this->app)['id']
        );

        $model = new EdgecreatorModeles2();
        $edgeCreatorEntityManager->persist(
            $model
                ->setPays('fr')
                ->setMagazine('DDD')
                ->setOrdre(1)
                ->setNomFonction('Remplir')
                ->setOptionNom('Couleur')
        );
        $edgeCreatorEntityManager->flush();
        $idOption = $model->getId();

        $value = new EdgecreatorValeurs();
        $edgeCreatorEntityManager->persist(
            $value
                ->setIdOption($idOption)
                ->setOptionValeur('#FF0000')
        );
        $edgeCreatorEntityManager->flush();
        $valueId = $value->getId();

        $interval = new EdgecreatorIntervalles();
        $edgeCreatorEntityManager->persist(
            $interval
                ->setIdValeur($valueId)
                ->setNumeroDebut(1)
                ->setNumeroFin(3)
                ->setUsername($edgeCreatorUser->getUsername())
        );

        $edgeCreatorEntityManager->flush();

        // Models v2

        $ongoingModel1 = new TranchesEnCoursModeles();
        $edgeCreatorEntityManager->persist(
            $ongoingModel1
                ->setPays('fr')
                ->setMagazine('PM')
                ->setNumero('502')
                ->setUsername($edgeCreatorUser->getUsername())
        );
        $edgeCreatorEntityManager->flush();

        $ongoingModel2 = new TranchesEnCoursModeles();
        $edgeCreatorEntityManager->persist(
            $ongoingModel2
                ->setPays('fr')
                ->setMagazine('PM')
                ->setNumero('503')
                ->setUsername(null)
        );

        $edgePicture = new ImagesTranches();
        $edgeCreatorEntityManager->persist(
            $edgePicture
                ->setNomfichier('photo1.jpg')
                ->setDateheure(new \DateTime('today'))
                ->setHash(sha1('test'))
                ->setIdUtilisateur(self::getSessionUser($this->app)['id'])
        );

        $ongoingModel2MainEdgePicture = new TranchesEnCoursModelesImages();
        $edgeCreatorEntityManager->persist(
            $ongoingModel2MainEdgePicture
                ->setModele($ongoingModel2)
                ->setImage($edgePicture)
                ->setEstphotoprincipale(true)
        );

        $edgeCreatorEntityManager->flush();

        $ongoingModel2Contributor1 = new TranchesEnCoursContributeurs();
        $edgeCreatorEntityManager->persist(
            $ongoingModel2Contributor1
                ->setModele($ongoingModel2)
                ->setIdUtilisateur($edgeCreatorUser->getId())
                ->setContribution('photographe')
        );

        $ongoingModel1Step1Value1 = new TranchesEnCoursValeurs();
        $edgeCreatorEntityManager->persist(
            $ongoingModel1Step1Value1
                ->setIdModele($ongoingModel1)
                ->setOrdre(1)
                ->setNomFonction('Remplir')
                ->setOptionNom('Couleur')
                ->setOptionValeur('#FF00FF')
        );

        $ongoingModel1Step1Value2 = new TranchesEnCoursValeurs();
        $edgeCreatorEntityManager->persist(
            $ongoingModel1Step1Value2
                ->setIdModele($ongoingModel1)
                ->setOrdre(1)
                ->setNomFonction('Remplir')
                ->setOptionNom('Pos_x')
                ->setOptionValeur('0')
        );

        $ongoingModel1Step2Value1 = new TranchesEnCoursValeurs();
        $edgeCreatorEntityManager->persist(
            $ongoingModel1Step2Value1
                ->setIdModele($ongoingModel1)
                ->setOrdre(2)
                ->setNomFonction('TexteMyFonts')
                ->setOptionNom('Couleur_texte')
                ->setOptionValeur('#000000')
        );
        $edgeCreatorEntityManager->flush();

        $ongoingModel3 = new TranchesEnCoursModeles();
        $edgeCreatorEntityManager->persist(
            $ongoingModel3
                ->setPays('fr')
                ->setMagazine('MP')
                ->setNumero('400')
                ->setUsername(null)
        );

        $ongoingModel4 = new TranchesEnCoursModeles();
        $edgeCreatorEntityManager->persist(
            $ongoingModel4
                ->setPays('fr')
                ->setMagazine('MP')
                ->setNumero('401')
                ->setUsername(null)
        );

        $ongoingModel4Contributor1 = new TranchesEnCoursContributeurs();
        $edgeCreatorEntityManager->persist(
            $ongoingModel4Contributor1
                ->setModele($ongoingModel4)
                ->setIdUtilisateur($edgeCreatorUser->getId())
                ->setContribution('createur')
        );

        $edgeCreatorEntityManager->flush();
    }

    protected static function createCoverIds()
    {
        $coverIdEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COVER_ID];

        $coverIds = [];
        $coverUrls = [];

        $urls = [
            'fr/DDD 1' => '2010/12/fr_ddd_001a_001.jpg',
            'fr/DDD 2' => '2010/12/fr_ddd_002a_001.jpg',
            'fr/MP 300' => '2010/12/fr_mp_0300a_001.jpg',
            'fr/XXX 111' => '2010/12/fr_xxx_111_001.jpg'
        ];

        foreach($urls as $issueNumber => $url) {
            $cover = new Covers();
            $coverIdEntityManager->persist(
                $cover
                    ->setSitecode('webusers')
                    ->setIssuecode($issueNumber)
                    ->setUrl($url)
            );
            $coverIdEntityManager->flush();
            $coverIds[]= $cover->getId();
            $coverUrls[$cover->getId()]= $url;

            @mkdir(DmServer::$settings['image_remote_root'].dirname($url), 0777, true);
            $imagePath = self::getPathToFileToUpload(self::$exampleImage);
            copy($imagePath, DmServer::$settings['image_remote_root'] . $url);
        }

        return [$coverIds, $coverUrls];
    }

    protected function createEntryLike($storyCode, $entryUrl, $publicationCode, $issueNumber) {
        $coaEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COA];

        // Create origin entry / entryurl / storyversion

        $originalEntryCode = $storyCode.'-entry-1';
        $originEntry = new InducksEntry();
        $coaEntityManager->persist(
            $originEntry
                ->setEntrycode($originalEntryCode)
                ->setStoryversioncode($storyCode.'-1')
        );

        $originEntryurl = new InducksEntryurl();
        $coaEntityManager->persist(
            $originEntryurl
                ->setEntrycode($originalEntryCode)
                ->setUrl($entryUrl)
        );

        $originStoryversion = new InducksStoryversion();
        $coaEntityManager->persist(
            $originStoryversion
                ->setStorycode($storyCode)
                ->setStoryversioncode($storyCode.'-1')
        );

        // Create similar entry / entryurl / storyversion

        $relatedEntryCode = $storyCode.'-entry-2';

        $relatedStoryversion = new InducksStoryversion();
        $coaEntityManager->persist(
            $relatedStoryversion
                ->setStorycode($storyCode)
                ->setStoryversioncode($storyCode.'-2')
        );

        $relatedEntry = new InducksEntry();
        $coaEntityManager->persist(
            $relatedEntry
                ->setEntrycode($relatedEntryCode)
                ->setIssuecode($publicationCode.' '.$issueNumber)
                ->setStoryversioncode($storyCode.'-2')
        );

        $relatedIssue = new InducksIssue();
        $coaEntityManager->persist(
            $relatedIssue
                ->setIssuecode($publicationCode.' '.$issueNumber)
                ->setPublicationcode($publicationCode)
                ->setIssuenumber($issueNumber)
        );

        $relatedEntryUrl = new InducksEntryurl();
        $coaEntityManager->persist(
            $relatedEntryUrl
                ->setEntrycode($relatedEntryCode)
                ->setUrl($entryUrl.'-2')
        );

        $coaEntityManager->flush();

        $coverIdEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COVER_ID];

        $relatedCover = new Covers();
        $coverIdEntityManager->persist(
            $relatedCover
                ->setUrl($entryUrl.'-2')
                ->setSitecode('webusers')
                ->setIssuecode($publicationCode.' '.$issueNumber)
        );

        $coverIdEntityManager->flush();

    }

    protected static function createEdgesData()
    {
        $dmEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_DM];

        $edge1 = new TranchesPretes();
        $dmEntityManager->persist(
            $edge1
                ->setPublicationcode('fr/JM')
                ->setIssuenumber('3001')
        );

        $dupEdge1 = new TranchesDoublons();
        $dmEntityManager->persist(
            $dupEdge1
                ->setPays('fr')
                ->setMagazine('JM')
                ->setNumero('3002')
                ->setTranchereference($edge1)
        );

        $edge2 = new TranchesPretes();
        $dmEntityManager->persist(
            $edge2
                ->setPublicationcode('fr/JM')
                ->setIssuenumber('4001')
        );

        $dupEdge2 = new TranchesDoublons();
        $dmEntityManager->persist(
            $dupEdge2
                ->setPays('fr')
                ->setMagazine('JM')
                ->setNumero('4002')
                ->setTranchereference($edge2)
        );

        $dmEntityManager->flush();
    }

    protected static function getPathToFileToUpload($fileName) {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $fileName]);
    }

    /**
     * @param Application $app
     * @param $userInfo array
     */
    protected static function setSessionUser(Application $app, $userInfo) {
        $app['session']->set('user', $userInfo);
    }
}