<?php
namespace DmServer\Test;

use Dm\Models\Users;
use DmServer\DmServer;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends TestCommon
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
}
