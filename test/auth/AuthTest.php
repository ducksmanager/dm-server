<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;

class AuthTest extends TestCommon
{
    public function testCallServiceWithoutSystemCredentials() {
        $response = $this->buildService('/collection/new', [], [], [], 'POST')->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithoutClientVersion() {
        $response = $this->buildService('/collection/new', [], [], $this->getDefaultSystemCredentialsNoVersion(),
            'POST')->call();
        $this->assertEquals(Response::HTTP_VERSION_NOT_SUPPORTED, $response->getStatusCode());
    }

    public function testCallServiceWithoutUserCredentials() {
        $response = $this->buildAuthenticatedService('/collection/new', [], [])->call();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCallServiceWithUserCredentials() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/new', 'POST', [
            'username' => 'dm_user',
            'password' => 'test',
            'password2' => 'test',
            'email' => 'test'
        ])->call();
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertNotEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
