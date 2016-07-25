<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;
use Wtd\Models\Users;

class CollectionTest extends TestCommon
{
    /**
     * @param string $username
     * @return null|Response
     */
    private function callAddIssue($username = 'dm_user')
    {
        return $this->buildAuthenticatedService('/collection/add', [
            'username' => $username,
            'password' => sha1('dm_pass')
        ], [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumber' => '1',
            'condition' => 'bon'
        ])->call();
    }

    public function testCreateCollection() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/new', 'POST', [
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $usersWithUsername = self::$em->getRepository(Users::class)->findBy(array('username' => 'dm_user'));

        $this->assertEquals(1, count($usersWithUsername));
        $this->assertEquals(Users::class, get_class($usersWithUsername[0]));
        $this->assertEquals(sha1('dm_pass'), $usersWithUsername[0]->getPassword());
    }

    public function testCreateCollectionErrorDifferentPasswords() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/new', 'POST', [
            'password2' => 'dm_pass_different',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortUsername() {
        $response = $this->buildAuthenticatedService('/collection/new', [
            'username' => 'dm',
            'password' => 'dm_pass'
        ], [
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortPassword() {
        $response = $this->buildAuthenticatedService('/collection/new', [
            'username' => 'dm_user',
            'password' => 'pass'
        ], [
            'password2' => 'pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorExistingUsername() {
        $this->createTestCollection();
        $response = $this->buildAuthenticatedService('/collection/new', [
            'username' => 'dm_user',
            'password' => 'dm_pass'
        ], [
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function testAddIssue() {
        $this->assertEquals(0, count($this->getCurrentUserIssues()));

        $this->createTestCollection('dm_user');

        $response = $this->callAddIssue('dm_user');

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(1, count($this->getCurrentUserIssues()));
    }

    public function testAddIssueNonExistingCollection() {
        $this->assertEquals(0, count($this->getCurrentUserIssues()));

        $this->createTestCollection('dm_user');

        $response = $this->callAddIssue('dm_user2');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
