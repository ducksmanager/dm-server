<?php
namespace DmServer\Test;

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
}
