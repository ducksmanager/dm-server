<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../test_bootstrap.php';

class AuthTest extends TestCommon
{
    public function testCallServiceWithoutSystemCredentials() {
        $response = $this->callService('/collection/new', [], array());
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithoutUserCredentials() {
        $response = $this->callAuthenticatedService('/collection/new', [], []);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCallServiceWithUserCredentials() {
        $response = $this->callAuthenticatedService('/collection/new', [
            'username' => 'dm_user',
            'password' => 'dm_pass'
        ], [
            'password2' => 'test',
            'email' => 'test'
        ]);
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertNotEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
