<?php
namespace DmServer\Test;

use Dm\Models\EmailsVentes;
use Dm\Models\Users;
use DmServer\DmServer;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends TestCommon
{
    public function testCallServiceWithoutSystemCredentials() {
        $response = $this->buildService('/collection/issues', [], [], [], 'POST')->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithoutClientVersion() {
        $response = $this->buildService('/collection/issues', [], [], static::getSystemCredentialsNoVersion(self::$dmUser),
            'POST')->call();
        $this->assertEquals(Response::HTTP_VERSION_NOT_SUPPORTED, $response->getStatusCode());
    }

    public function testCallServiceWithoutUserCredentials() {
        $response = $this->buildAuthenticatedService('/collection/issues', self::$dmUser, [], [])->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithWrongUserCredentials() {
        $response = $this->buildAuthenticatedService('/collection/issues', self::$dmUser, ['username' => 'dm_test',
            'password' => 'invalid'], [])->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithUserCredentials() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/ducksmanager/user/new', self::$dmUser, 'POST', [
            'username' => 'dm_user',
            'password' => 'test',
            'password2' => 'test',
            'email' => 'test'
        ])->call();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertNotEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCreateCollection() {
        $response = $this->buildAuthenticatedService('/ducksmanager/user/new', self::$dmUser, [], [
            'username' => self::$defaultTestDmUserName,
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        /** @var Users[] $usersWithUsername */
        try {
            $usersWithUsername = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(
                ['username' => self::$defaultTestDmUserName]
            );
        } catch (DBALException $e) {
        } catch (ORMException $e) {
        }

        $this->assertCount(1, $usersWithUsername);
        $this->assertEquals(Users::class, get_class($usersWithUsername[0]));
        $this->assertEquals(sha1('dm_pass'), $usersWithUsername[0]->getPassword());
    }

    public function testCreateCollectionErrorDifferentPasswords() {
        $response = $this->buildAuthenticatedService('/ducksmanager/user/new', self::$dmUser, [], [
            'username' => self::$defaultTestDmUserName,
            'password' => 'dm_pass',
            'password2' => 'dm_pass_different',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortUsername() {
        $response = $this->buildAuthenticatedService('/ducksmanager/user/new', self::$dmUser, [], [
            'username' => 'dm',
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortPassword() {
        $response = $this->buildAuthenticatedService('/ducksmanager/user/new', self::$dmUser, [], [
            'username' => self::$defaultTestDmUserName,
            'password' => 'pass',
            'password2' => 'pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorExistingUsername() {
        self::createTestCollection();
        $response = $this->buildAuthenticatedService('/ducksmanager/user/new', self::$dmUser, [], [
            'username' => self::$defaultTestDmUserName,
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function testCreateSaleEmail() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        $otherUsername = 'otheruser';
        self::createTestCollection($otherUsername);

        $response = $this->buildAuthenticatedServiceWithTestUser("/user/sale/$otherUsername", self::$dmUser, 'POST')->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testCreateSaleEmailInvalidUser() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        $response = $this->buildAuthenticatedServiceWithTestUser('/user/sale/testuser', self::$dmUser, 'POST')->call();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testGetSaleEmail() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        $otherUsername = 'otheruser';
        self::createTestCollection($otherUsername);

        $this->buildAuthenticatedServiceWithTestUser("/user/sale/$otherUsername", self::$dmUser, 'POST')->call();

        $today = new \DateTime('today');
        $today = $today->format('Y-m-d');
        $response = $this->buildAuthenticatedServiceWithTestUser("/user/sale/$otherUsername/$today", self::$dmUser, 'GET')->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertCount(1, $objectResponse);
        /** @var EmailsVentes $access */
        $access = unserialize($objectResponse[0]);
        $this->assertEquals($collectionUserInfo['username'], $access->getUsernameVente());
        $this->assertEquals($otherUsername, $access->getUsernameAchat());
        $this->assertEquals(new \DateTime('today'), $access->getDate());
    }
}
