<?php
namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;

class ServiceTest extends TestCommon
{
    public function testCallServiceWithoutSystemCredentials(): void
    {
        $response = $this->buildService(
            '/coa/list/countries/fr', [
                'username' => self::$defaultTestDmUserName,
                'password' => sha1(self::$testDmUsers[self::$defaultTestDmUserName])
            ],
            [],
            ['HTTP_AUTHORIZATION' => 'Basic '.base64_encode(self::$dmUser.':invalid')],
            'GET')->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
