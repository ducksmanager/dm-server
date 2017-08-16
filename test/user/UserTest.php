<?php
namespace DmServer\Test;

use Dm\Models\Achats;
use Dm\Models\EmailsVentes;
use Dm\Models\Numeros;
use Dm\Models\Users;
use DmServer\DmServer;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends TestCommon
{
    public function testCallServiceWithoutSystemCredentials() {
        $response = $this->buildService('/collection/issues', [], [], [], 'POST')->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithoutClientVersion() {
        $response = $this->buildService('/collection/issues', [], [], $this->getSystemCredentialsNoVersion(TestCommon::$dmUser),
            'POST')->call();
        $this->assertEquals(Response::HTTP_VERSION_NOT_SUPPORTED, $response->getStatusCode());
    }

    public function testCallServiceWithoutUserCredentials() {
        $response = $this->buildAuthenticatedService('/collection/issues', TestCommon::$dmUser, [], [])->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithWrongUserCredentials() {
        $response = $this->buildAuthenticatedService('/collection/issues', TestCommon::$dmUser, ['username' => 'dm_test',
            'password' => 'invalid'], [])->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithUserCredentials() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/user/new', TestCommon::$dmUser, 'POST', [
            'username' => 'dm_user',
            'password' => 'test',
            'password2' => 'test',
            'email' => 'test'
        ])->call();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertNotEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCreateCollection() {
        $response = $this->buildAuthenticatedService('/user/new', TestCommon::$dmUser, [], [
            'username' => self::$defaultTestDmUserName,
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        /** @var Users[] $usersWithUsername */
        $usersWithUsername = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(
            ['username' => self::$defaultTestDmUserName]
        );

        $this->assertEquals(1, count($usersWithUsername));
        $this->assertEquals(Users::class, get_class($usersWithUsername[0]));
        $this->assertEquals(sha1('dm_pass'), $usersWithUsername[0]->getPassword());
    }

    public function testCreateCollectionErrorDifferentPasswords() {
        $response = $this->buildAuthenticatedService('/user/new', TestCommon::$dmUser, [], [
            'username' => self::$defaultTestDmUserName,
            'password' => 'dm_pass',
            'password2' => 'dm_pass_different',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortUsername() {
        $response = $this->buildAuthenticatedService('/user/new', TestCommon::$dmUser, [], [
            'username' => 'dm',
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortPassword() {
        $response = $this->buildAuthenticatedService('/user/new', TestCommon::$dmUser, [], [
            'username' => self::$defaultTestDmUserName,
            'password' => 'pass',
            'password2' => 'pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorExistingUsername() {
        self::createTestCollection();
        $response = $this->buildAuthenticatedService('/user/new', TestCommon::$dmUser, [], [
            'username' => self::$defaultTestDmUserName,
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function testResetDemoDataWrongUser() {
        $response = $this->buildAuthenticatedService('/user/resetDemo', TestCommon::$dmUser, [])->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testResetDemoDataNoDemoUser() {
        $response = $this->buildAuthenticatedService('/user/resetDemo', TestCommon::$adminUser, [])->call();
        $this->assertEquals(Response::HTTP_EXPECTATION_FAILED, $response->getStatusCode());
    }

    public function testResetDemoData() {
        self::createTestCollection('demo');

        $dmEm = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM);

        $demoUser = $dmEm->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $purchasesOfDemoUser = $dmEm->getRepository(Achats::class)->findBy([
            'idUser' => $demoUser->getId()
        ]);

        $this->assertEquals(1, count(array_filter($purchasesOfDemoUser, function(Achats $purchase) {
            return $purchase->getDate()->format('Y-m-d') === '2010-01-01' && $purchase->getDescription() === 'Purchase';
        })));

        $issuesOfDemoUser = $dmEm->getRepository(Numeros::class)->findBy([
            'idUtilisateur' => $demoUser->getId()
        ]);
        $this->assertEquals(1, count(array_filter($issuesOfDemoUser, function(Numeros $issue) {
            return $issue->getPays() === 'fr' && $issue->getMagazine() === 'MP' && $issue->getNumero() === '300';
        })));

        $demoUser->setBibliothequeTexture1('A');
        $demoUser->setBibliothequeSousTexture1('B');
        $demoUser->setBibliothequeTexture2('C');
        $demoUser->setBibliothequeSousTexture2('D');
        $demoUser->setBibliothequeGrossissement(1);
        $demoUser->setBetauser(true);
        $dmEm->flush($demoUser);

        $response = $this->buildAuthenticatedService('/user/resetDemo', TestCommon::$adminUser, [])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $demoUser = $dmEm->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $this->assertEquals('bois', $demoUser->getBibliothequeTexture1());
        $this->assertEquals('HONDURAS MAHOGANY', $demoUser->getBibliothequeSousTexture1());
        $this->assertEquals('bois', $demoUser->getBibliothequeTexture2());
        $this->assertEquals('KNOTTY PINE', $demoUser->getBibliothequeSousTexture2());
        $this->assertEquals(1.5, $demoUser->getBibliothequeGrossissement());

        $this->assertEquals(true, $demoUser->getBetauser()); // This property shouldn't have reset

        $issuesOfDemoUser = $dmEm->getRepository(Numeros::class)->findBy([
            'idUtilisateur' => $demoUser->getId()
        ]);

        $this->assertEquals(35, count($issuesOfDemoUser));
        $this->assertEquals(0, count(array_filter($issuesOfDemoUser, function(Numeros $issue) {
            return $issue->getPays() === 'fr' && $issue->getMagazine() === 'MP' && $issue->getNumero() === '300';
        }))); // Previous issue has been reset

        $purchasesOfDemoUser = $dmEm->getRepository(Achats::class)->findBy([
            'idUser' => $demoUser->getId()
        ]);

        $this->assertEquals(4, count($purchasesOfDemoUser));
        $this->assertEquals(0, count(array_filter($purchasesOfDemoUser, function(Achats $purchase) {
            return $purchase->getDate()->format('Y-m-d') === '2010-01-01' && $purchase->getDescription() === 'Purchase';
        }))); // Previous issue has been reset
    }

    public function testCreateSaleEmail() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        $otherUsername = 'otheruser';
        self::createTestCollection($otherUsername);

        $response = $this->buildAuthenticatedServiceWithTestUser("/user/sale/$otherUsername", TestCommon::$dmUser, 'POST')->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testCreateSaleEmailInvalidUser() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        $response = $this->buildAuthenticatedServiceWithTestUser('/user/sale/testuser', TestCommon::$dmUser, 'POST')->call();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testGetSaleEmail() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        $otherUsername = 'otheruser';
        self::createTestCollection($otherUsername);

        $this->buildAuthenticatedServiceWithTestUser("/user/sale/$otherUsername", TestCommon::$dmUser, 'POST')->call();

        $today = new \DateTime('today');
        $today = $today->format('Y-m-d');
        $response = $this->buildAuthenticatedServiceWithTestUser("/user/sale/$otherUsername/$today", TestCommon::$dmUser, 'GET')->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(1, count($objectResponse));
        /** @var EmailsVentes $access */
        $access = unserialize($objectResponse[0]);
        $this->assertEquals($collectionUserInfo['username'], $access->getUsernameVente());
        $this->assertEquals($otherUsername, $access->getUsernameAchat());
        $this->assertEquals(new \DateTime('today'), $access->getDate());
    }
}
