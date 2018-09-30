<?php
namespace DmServer\Test;

use DmServer\DmServer;
use Symfony\Component\HttpFoundation\Response;

class RawSqlTest extends TestCommon
{
    public function setUp()
    {
        parent::setUp();
        self::createTestCollection('dm_test_user');
    }

    public function testRawSqlWithUserWithoutPermission() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$dmUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => DmServer::CONFIG_DB_KEY_DM
        ])
            ->call();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRawSqlWithUserWithBadDbParameter() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => 'db_wrong'
        ]);
        ob_start();
        $response = $service->call();
        ob_end_clean();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testRawSql() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => DmServer::CONFIG_DB_KEY_DM
        ])->call();

        $objectResponse = json_decode($this->getResponseContent($response), true);

        $this->assertInternalType('array', $objectResponse);
        $this->assertCount(3, $objectResponse);
        $this->assertInternalType('array', $objectResponse[0]);
        $this->assertEquals('fr', $objectResponse[0]['Pays']);
        $this->assertEquals('DDD', $objectResponse[0]['Magazine']);
    }

    public function testRawSqlWithParameters() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros WHERE Magazine=:Magazine',
            'parameters' => ['Magazine' => 'DDD'],
            'db'    => DmServer::CONFIG_DB_KEY_DM
        ])->call();

        $objectResponse = json_decode($this->getResponseContent($response), true);

        $this->assertInternalType('array', $objectResponse);
        $this->assertCount(1, $objectResponse);
        $this->assertInternalType('array', $objectResponse[0]);
        $this->assertEquals('fr', $objectResponse[0]['Pays']);
        $this->assertEquals('DDD', $objectResponse[0]['Magazine']);
    }

    public function testRawSqlInvalidSelect() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT invalid FROM numeros',
            'db'    => DmServer::CONFIG_DB_KEY_DM
        ])->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
            $this->assertStringStartsWith('An exception occurred while executing', $response->getContent());
        });
    }

    public function testRawSqlMultipleStatements() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros; DELETE FROM numeros',
            'db'    => DmServer::CONFIG_DB_KEY_DM
        ])
            ->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertEquals('Raw queries shouldn\'t contain the ";" symbol', $response->getContent());
        });
    }
}
