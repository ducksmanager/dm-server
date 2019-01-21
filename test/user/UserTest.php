<?php
namespace DmServer\Test;

use Countable;
use Dm\Models\EmailsVentes;
use Dm\Models\Users;
use DmServer\DmServer;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends TestCommon
{
    protected function getEm() {
        return parent::getEntityManagerByName(DmServer::CONFIG_DB_KEY_DM);
    }

    public function testCallServiceWithoutSystemCredentials() {
        $response = $this->buildService('/collection/issues')->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithoutClientVersion() {
        $response = $this->buildService('/collection/issues', [], [], static::getSystemCredentialsNoVersion(self::$dmUser))->call();
        $this->assertEquals(Response::HTTP_VERSION_NOT_SUPPORTED, $response->getStatusCode());
    }

    public function testCallServiceWithoutUserCredentials() {
        $response = $this->buildAuthenticatedService('/collection/issues', self::$dmUser, [])->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithWrongUserCredentials() {
        $response = $this->buildAuthenticatedService('/collection/issues', self::$dmUser, ['username' => 'dm_test',
            'password' => 'invalid'])->call();
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

        /** @var $usersWithUsername Users[]|Countable */
        $usersWithUsername = $this->getEm()->getRepository(Users::class)->findBy(
            ['username' => self::$defaultTestDmUserName]
        );

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
        $user = self::createTestCollection();
        self::setSessionUser($this->app, $user);

        $otherUsername = 'otheruser';
        self::createTestCollection($otherUsername);

        $response = $this->buildAuthenticatedServiceWithTestUser("/user/sale/$otherUsername", self::$dmUser, 'POST')->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testCreateSaleEmailInvalidUser() {
        $user = self::createTestCollection();
        self::setSessionUser($this->app, $user);

        $response = $this->buildAuthenticatedServiceWithTestUser('/user/sale/testuser', self::$dmUser, 'POST')->call();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testGetSaleEmail() {
        $user = self::createTestCollection();
        self::setSessionUser($this->app, $user);

        $otherUser = self::createTestCollection('otheruser');

        $today = new \DateTime();
        $today->setTime(0, 0);

        $sale = new EmailsVentes();
        $this->getEm()->persist(
            $sale
                ->setUsernameVente($user->getUsername())
                ->setUsernameAchat($otherUser->getUsername())
                ->setDate($today)
        );
        $this->getEm()->flush();

        $todayStr = $today->format('Y-m-d');
        $response = $this->buildAuthenticatedServiceWithTestUser("/user/sale/{$otherUser->getUsername()}/$todayStr", self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertCount(1, $objectResponse);
        /** @var EmailsVentes $access */
        $access = unserialize($objectResponse[0]);
        $this->assertEquals($user->getUsername(), $access->getUsernameVente());
        $this->assertEquals($otherUser->getUsername(), $access->getUsernameAchat());
        $this->assertEquals($today, $access->getDate());
    }
}
