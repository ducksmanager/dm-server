<?php
namespace DmServer\Test;

use Symfony\Component\HttpFoundation\Response;

class ServiceTest extends TestCommon
{
    public function testCallServiceWithoutSystemCredentials() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/internal/coa/countrynames/fr', self::$dmUser)->call();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
