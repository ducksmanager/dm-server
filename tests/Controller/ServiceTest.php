<?php
namespace App\Tests;

use App\Tests\Fixtures\CoaEntryFixture;
use App\Tests\Fixtures\CoaFixture;
use Symfony\Component\HttpFoundation\Response;

class ServiceTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['coa'];
    }

    public function setUp()
    {
        parent::setUp();
        $this->loadFixture('coa', new CoaFixture());
        $this->loadFixture('coa', new CoaEntryFixture());
    }

    public function testCallServiceWithoutSystemCredentials(): void
    {
        $response = $this->buildService(
            '/collection/user', [
                'username' => self::$defaultTestDmUserName,
                'password' => sha1(self::$testDmUsers[self::$defaultTestDmUserName])
            ],
            [],
            ['HTTP_AUTHORIZATION' => 'Basic '.base64_encode(self::$dmUser.':invalid')],
            'GET')->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
