<?php
namespace DmServer\Test;

use Symfony\Component\HttpFoundation\Response;

class RawSqlTest extends TestCommon
{
    public function setUp()
    {
        parent::setUp();
        self::createTestCollection('dm_user');
    }

    public function testRawSqlWithUserWithoutPermission() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/rawsql', TestCommon::$testUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => 'db'
        ]);
        $response = $service->call();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRawSqlWithUserWithBadDbParameter() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/rawsql', TestCommon::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => 'db_wrong'
        ]);
        ob_start();
        $response = $service->call();
        ob_end_clean();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testRawSql() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/rawsql', TestCommon::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => 'db'
        ]);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $objectResponse);
        $this->assertEquals(3, count($objectResponse));
        $this->assertInternalType('array', $objectResponse[0]);
        $this->assertEquals('fr', $objectResponse[0]['Pays']);
        $this->assertEquals('DDD', $objectResponse[0]['Magazine']);
    }
}
