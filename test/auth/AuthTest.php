<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;

class AuthTest extends TestCommon
{
    public function testCallServiceWithoutSystemCredentials() {
        $response = $this->callService('/collection/new', [], array());
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithoutClientVersion() {
        $response = $this->callService('/collection/new', [], [], $this->getDefaultSystemCredentialsNoVersion());
        $this->assertEquals(Response::HTTP_VERSION_NOT_SUPPORTED, $response->getStatusCode());
    }

    public function testCallServiceWithoutUserCredentials() {
        $response = $this->callAuthenticatedService('/collection/new', [], []);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCallServiceWithUserCredentials() {
        $response = $this->callAuthenticatedServiceWithTestUser('/collection/new', [
            'password2' => 'test',
            'email' => 'test'
        ]);
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertNotEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
