<?php
namespace App\Tests\Controller;

use App\Tests\Fixtures\DmCollectionFixture;
use App\Tests\TestCommon;
use Symfony\Component\HttpFoundation\Response;

class RawSqlTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm'];
    }

    public function setUp()
    {
        parent::setUp();
        DmCollectionFixture::$username = self::$defaultTestDmUserName;
        $this->loadFixtures([DmCollectionFixture::class], true, 'dm');
    }

    public function testRawSqlWithUserWithoutPermission(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$dmUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => 'dm'
        ])
            ->call();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRawSqlWithUserWithBadDbParameter(): void
    {
        $service = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => 'db_wrong'
        ]);
        ob_start();
        $response = $service->call();
        ob_end_clean();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testRawSql(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros',
            'db'    => 'dm'
        ])->call();

        $objectResponse = json_decode($this->getResponseContent($response), true);

        $this->assertInternalType('array', $objectResponse);
        $this->assertCount(3, $objectResponse);
        $this->assertInternalType('array', $objectResponse[0]);
        $this->assertEquals('fr', $objectResponse[0]['Pays']);
        $this->assertEquals('DDD', $objectResponse[0]['Magazine']);
    }

    public function testRawSqlWithParameters(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros WHERE Magazine=:Magazine',
            'parameters' => ['Magazine' => 'DDD'],
            'db'    => 'dm'
        ])->call();

        $objectResponse = json_decode($this->getResponseContent($response), true);

        $this->assertInternalType('array', $objectResponse);
        $this->assertCount(1, $objectResponse);
        $this->assertInternalType('array', $objectResponse[0]);
        $this->assertEquals('fr', $objectResponse[0]['Pays']);
        $this->assertEquals('DDD', $objectResponse[0]['Magazine']);
    }

    public function testRawSqlWithEncodedParameters(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'INSERT INTO bouquineries(Nom, AdresseComplete, Commentaire, ID_Utilisateur, CoordX, CoordY, Actif)
                        VALUES (:nom, :adresse_complete, :commentaire, :id_user, :coordX, :coordY, 0)',
            'parameters' => json_encode([
                ':nom' => 'test',
                ':adresse_complete' => 'Test place, Paris',
                ':commentaire' => 'cool',
                ':id_user' => NULL,
                ':coordX' => '50',
                ':coordY' => '60',
            ]),
            'db'    => 'dm'
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testRawSqlInvalidSelect(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT invalid FROM numeros',
            'db'    => 'dm'
        ])->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
            $this->assertStringStartsWith('An exception occurred while executing', $response->getContent());
        });
    }

    public function testRawSqlMultipleStatements(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/rawsql', self::$rawSqlUser, 'POST', [
            'query' => 'SELECT * FROM numeros; DELETE FROM numeros',
            'db'    => 'dm'
        ])
            ->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertEquals('Raw queries shouldn\'t contain the ";" symbol', $response->getContent());
        });
    }
}
