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
use CoverId\Models\Covers;
use Dm\Models\Achats;
use Dm\Models\Numeros;
use Dm\Models\Users;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmStats\Models\AuteursHistoires;
use DmStats\Models\AuteursPseudosSimple;
use DmStats\Models\UtilisateursHistoiresManquantes;
use DmStats\Models\UtilisateursPublicationsManquantes;
use DmStats\Models\UtilisateursPublicationsSuggerees;
use EdgeCreator\Models\EdgecreatorIntervalles;
use EdgeCreator\Models\EdgecreatorModeles2;
use EdgeCreator\Models\EdgecreatorValeurs;
use EdgeCreator\Models\TranchesEnCoursModeles;
use EdgeCreator\Models\TranchesEnCoursValeurs;
use Silex\Application;
use Silex\WebTestCase;

class TestCommon extends WebTestCase {

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

    // Test data - end

    public static function setUpBeforeClass()
    {
        DmServer::initSettings('settings.test.ini');
        self::$conf = DmServer::getAppConfig(true);
        self::$roles = DmServer::getAppRoles(true);
    }

    public function setUp() {
        DmServer::$entityManagers = [];

        foreach(DmServer::$configuredEntityManagerNames as $emName) {
            self::$schemas[$emName] = SchemaWithClasses::createFromEntityManager(DmServer::getEntityManager($emName, true));
        }

        self::initDatabase();
        @rmdir(DmServer::$settings['image_local_root']);
        @mkdir(DmServer::$settings['image_local_root'], 0777, true);
        parent::setUp();
    }

    protected static function initDatabase() {

        foreach(DmServer::$configuredEntityManagerNames as $emName) {
            self::$schemas[$emName]->recreateSchema();
            DmServer::getEntityManager($emName)->clear();
        }
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
        $service = new TestServiceCallCommon(static::createClient());
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
            ['idUtilisateur' => AbstractController::getSessionUser($this->app)['id']]
        );
    }

    protected static function createUser($username, $password) {
        $dmEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_DM];

        $user = new Users();
        $user->setBetauser(false);
        $user->setUsername($username);
        $user->setPassword(sha1($password));
        $user->setEmail('test@ducksmanager.net');
        $user->setDateinscription(\DateTime::createFromFormat('Y-m-d', '2000-01-01'));
        $dmEntityManager->persist($user);

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
        $numero1->setPays('fr');
        $numero1->setMagazine('DDD');
        $numero1->setNumero('1');
        $numero1->setEtat('indefini');
        $numero1->setIdAcquisition('-2');
        $numero1->setAv(false);
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

        $purchase1 = new Achats();
        $purchase1->setDate(\DateTime::createFromFormat('Y-m-d', '2010-01-01'));
        $purchase1->setDescription('Purchase');
        $purchase1->setIdUser($user->getId());
        $dmEntityManager->persist($purchase1);

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
                ->setIssuecode('fr/DDD 1'));

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

        $userId = AbstractController::getSessionUser($this->app)['id'];

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
            ->setNotation($authorUser1->getNotation()));

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
            AbstractController::getSessionUser($this->app)['id']
        );

        $model = new EdgecreatorModeles2();
        $model->setPays('fr');
        $model->setMagazine('DDD');
        $model->setOrdre(1);
        $model->setNomFonction('Remplir');
        $model->setOptionNom('Couleur');
        $edgeCreatorEntityManager->persist($model);
        $edgeCreatorEntityManager->flush();
        $idOption = $model->getId();

        $value = new EdgecreatorValeurs();
        $value->setIdOption($idOption);
        $value->setOptionValeur('#FF0000');
        $edgeCreatorEntityManager->persist($value);
        $edgeCreatorEntityManager->flush();
        $valueId = $value->getId();

        $interval = new EdgecreatorIntervalles();
        $interval->setIdValeur($valueId);
        $interval->setNumeroDebut(1);
        $interval->setNumeroFin(3);
        $interval->setUsername($edgeCreatorUser->getUsername());
        $edgeCreatorEntityManager->persist($interval);

        $edgeCreatorEntityManager->flush();

        // Models v2

        $ongoingModel1 = new TranchesEnCoursModeles();
        $ongoingModel1->setPays('fr');
        $ongoingModel1->setMagazine('PM');
        $ongoingModel1->setNumero('502');
        $ongoingModel1->setUsername($edgeCreatorUser->getUsername());
        $edgeCreatorEntityManager->persist($ongoingModel1);
        $edgeCreatorEntityManager->flush();

        $ongoingModel1Step1Value1 = new TranchesEnCoursValeurs();
        $ongoingModel1Step1Value1->setIdModele($ongoingModel1);
        $ongoingModel1Step1Value1->setOrdre(1);
        $ongoingModel1Step1Value1->setNomFonction('Remplir');
        $ongoingModel1Step1Value1->setOptionNom('Couleur');
        $ongoingModel1Step1Value1->setOptionValeur('#FF00FF');
        $edgeCreatorEntityManager->persist($ongoingModel1Step1Value1);

        $ongoingModel1Step1Value2 = new TranchesEnCoursValeurs();
        $ongoingModel1Step1Value2->setIdModele($ongoingModel1);
        $ongoingModel1Step1Value2->setOrdre(1);
        $ongoingModel1Step1Value2->setNomFonction('Remplir');
        $ongoingModel1Step1Value2->setOptionNom('Pos_x');
        $ongoingModel1Step1Value2->setOptionValeur('0');
        $edgeCreatorEntityManager->persist($ongoingModel1Step1Value2);

        $ongoingModel1Step2Value1 = new TranchesEnCoursValeurs();
        $ongoingModel1Step2Value1->setIdModele($ongoingModel1);
        $ongoingModel1Step2Value1->setOrdre(2);
        $ongoingModel1Step2Value1->setNomFonction('TexteMyFonts');
        $ongoingModel1Step2Value1->setOptionNom('Couleur_texte');
        $ongoingModel1Step2Value1->setOptionValeur('#000000');
        $edgeCreatorEntityManager->persist($ongoingModel1Step2Value1);
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
            $cover->setSitecode('webusers');
            $cover->setIssuecode($issueNumber);
            $cover->setUrl($url);
            $coverIdEntityManager->persist($cover);
            $coverIdEntityManager->flush();
            $coverIds[]= $cover->getId();
            $coverUrls[$cover->getId()]= $url;

            @mkdir(DmServer::$settings['image_remote_root'].dirname($url), 0777, true);
            $imagePath = self::getPathToFileToUpload(self::$exampleImage);
            file_put_contents(DmServer::$settings['image_remote_root'] . $url, file_get_contents($imagePath));
        }

        return [$coverIds, $coverUrls];
    }

    protected function createEntryLike($storyCode, $entryUrl, $publicationCode, $issueNumber) {
        $coaEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COA];

        // Create origin entry / entryurl / storyversion

        $originalEntryCode = $storyCode.'-entry-1';
        $originEntry = new InducksEntry();
        $originEntry->setEntrycode($originalEntryCode);
        $originEntry->setStoryversioncode($storyCode.'-1');
        $coaEntityManager->persist($originEntry);

        $originEntryurl = new InducksEntryurl();
        $originEntryurl->setEntrycode($originalEntryCode);
        $originEntryurl->setUrl($entryUrl);
        $coaEntityManager->persist($originEntryurl);

        $originStoryversion = new InducksStoryversion();
        $originStoryversion->setStorycode($storyCode);
        $originStoryversion->setStoryversioncode($storyCode.'-1');
        $coaEntityManager->persist($originStoryversion);

        // Create similar entry / entryurl / storyversion

        $relatedEntryCode = $storyCode.'-entry-2';

        $relatedStoryversion = new InducksStoryversion();
        $relatedStoryversion->setStorycode($storyCode);
        $relatedStoryversion->setStoryversioncode($storyCode.'-2');
        $coaEntityManager->persist($relatedStoryversion);

        $relatedEntry = new InducksEntry();
        $relatedEntry->setEntrycode($relatedEntryCode);
        $relatedEntry->setIssuecode($publicationCode.' '.$issueNumber);
        $relatedEntry->setStoryversioncode($storyCode.'-2');
        $coaEntityManager->persist($relatedEntry);

        $relatedIssue = new InducksIssue();
        $relatedIssue->setIssuecode($publicationCode.' '.$issueNumber);
        $relatedIssue->setPublicationcode($publicationCode);
        $relatedIssue->setIssuenumber($issueNumber);
        $coaEntityManager->persist($relatedIssue);

        $relatedEntryUrl = new InducksEntryurl();
        $relatedEntryUrl->setEntrycode($relatedEntryCode);
        $relatedEntryUrl->setUrl($entryUrl.'-2');
        $coaEntityManager->persist($relatedEntryUrl);

        $coaEntityManager->flush();

        $coverIdEntityManager = DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COVER_ID];

        $relatedCover = new Covers();
        $relatedCover->setUrl($entryUrl.'-2');
        $relatedCover->setSitecode('webusers');
        $relatedCover->setIssuecode($publicationCode.' '.$issueNumber);
        $coverIdEntityManager->persist($relatedCover);

        $coverIdEntityManager->flush();

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