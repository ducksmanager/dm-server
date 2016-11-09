<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;
use Wtd\Models\Users;
use Wtd\Wtd;

class CollectionTest extends TestCommon
{
    /**
     * @return null|Response
     */
    private function callAddIssue()
    {
        return $this->buildAuthenticatedServiceWithTestUser('/collection/add', TestCommon::$testUser, 'POST', [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumber' => '3',
            'condition' => 'bon'
        ])->call();
    }

    public function testCreateCollection() {
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm_user',
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $usersWithUsername = Wtd::getEntityManager(Wtd::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(
            array('username' => 'dm_user')
        );

        $this->assertEquals(1, count($usersWithUsername));
        $this->assertEquals(Users::class, get_class($usersWithUsername[0]));
        $this->assertEquals(sha1('dm_pass'), $usersWithUsername[0]->getPassword());
    }

    public function testCreateCollectionErrorDifferentPasswords() {
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm_user',
            'password' => 'dm_pass',
            'password2' => 'dm_pass_different',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortUsername() {
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm',
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortPassword() {
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm_user',
            'password' => 'pass',
            'password2' => 'pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorExistingUsername() {
        self::createTestCollection();
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm_user',
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function testAddIssue() {
        $this->assertEquals(0, count($this->getCurrentUserIssues()));

        self::createTestCollection('dm_user'); // Creates a collection with 3 issues

        $response = $this->callAddIssue();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(4, count($this->getCurrentUserIssues()));
    }
}
