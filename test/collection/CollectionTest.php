<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;
use Wtd\Models\Numeros;
use Wtd\Models\Users;

require __DIR__ . '/../test_bootstrap.php';

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
        $response = $this->callAuthenticatedService('/collection/new', [
            'username' => 'dm_user',
            'password' => 'dm_pass'
        ], [
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $usersWithUsername = self::$em->getRepository(Users::class)->findBy(array('username' => 'dm_user'));

        $this->assertEquals(1, count($usersWithUsername));
        $this->assertEquals(Users::class, get_class($usersWithUsername[0]));
        $this->assertEquals(sha1('dm_pass'), $usersWithUsername[0]->getPassword());
    }

    public function testCallServiceWithUserCredentialsRequired() {
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
