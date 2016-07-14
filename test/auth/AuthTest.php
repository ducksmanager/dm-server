<?php
namespace Wtd\Test;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../test_bootstrap.php';

class AuthTest extends TestCommon
{
    public function testCallServiceWithoutSystemCredentials() {
        $response = $this->callService([]);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallServiceWithoutCredentials() {
        $response = $this->callAuthenticatedService([]);
        $this->assertTrue($response->isClientError());
    }
}
