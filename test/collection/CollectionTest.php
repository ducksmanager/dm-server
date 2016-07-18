<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;
use Wtd\Models\Numeros;
use Wtd\Models\Users;

class CollectionTest extends TestCommon
{
    private function createTestCollection() {
        $user = new Users();
        $user->setUsername('dm_user');
        $user->setPassword(sha1('dm_pass'));
        $user->setEmail('test@ducksmanager.net');
        $user->setDateinscription(\DateTime::createFromFormat('Y-m-d', '2000-01-01'));
        self::$em->persist($user);
        self::$em->flush();
    }

    public function testCreateCollection() {
        $response = $this->callAuthenticatedServiceWithTestUser('/collection/new', [
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $usersWithUsername = self::$em->getRepository(Users::class)->findBy(array('username' => 'dm_user'));

        $this->assertEquals(1, count($usersWithUsername));
        $this->assertEquals(Users::class, get_class($usersWithUsername[0]));
        $this->assertEquals(sha1('dm_pass'), $usersWithUsername[0]->getPassword());
    }

    public function testCreateCollectionErrorDifferentPasswords() {
        $response = $this->callAuthenticatedServiceWithTestUser('/collection/new', [
            'password2' => 'dm_pass_different',
            'email' => 'test@ducksmanager.net'
        ]);
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortUsername() {
        $response = $this->callAuthenticatedService('/collection/new', [
            'username' => 'dm',
            'password' => 'dm_pass'
        ], [
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ]);
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortPassword() {
        $response = $this->callAuthenticatedService('/collection/new', [
            'username' => 'dm_user',
            'password' => 'pass'
        ], [
            'password2' => 'pass',
            'email' => 'test@ducksmanager.net'
        ]);
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorExistingUsername() {
        $this->createTestCollection();
        $response = $this->callAuthenticatedService('/collection/new', [
            'username' => 'dm_user',
            'password' => 'dm_pass'
        ], [
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ]);
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function testAddIssue() {
        $this->assertEquals(0, count($this->getCurrentUserIssues()));

        $this->createTestCollection();

        $response = $this->callAuthenticatedService('/collection/add', [
            'username' => 'dm_user',
            'password' => sha1('dm_pass')
        ], [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumber' => '1',
            'condition'   => 'bon'
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(1, count($this->getCurrentUserIssues()));
    }
}
