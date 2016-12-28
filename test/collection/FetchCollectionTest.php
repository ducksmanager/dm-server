<?php
namespace DmServer\Test;


class FetchCollectionTest extends TestCommon
{
    public function setUp()
    {
        parent::setUp();
        self::createTestCollection('dm_user');
        self::createCoaData();
    }

    public function testFetchCollection() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/collection/fetch', TestCommon::$testUser, 'GET');
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);

        $this->assertInternalType('object', $objectResponse->static);
        $this->assertInternalType('object', $objectResponse->static->pays);
        $this->assertEquals('France', $objectResponse->static->pays->fr);

        $this->assertInternalType('object', $objectResponse->static->magazines);
        $this->assertEquals('Dynastie', $objectResponse->static->magazines->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->static->magazines->{'fr/MP'});

        $this->assertInternalType('object', $objectResponse->numeros);
        $this->assertInternalType('array', $objectResponse->numeros->{'fr/DDD'});
        $this->assertEquals('1', $objectResponse->numeros->{'fr/DDD'}[0]->numero);
        $this->assertEquals('indefini', $objectResponse->numeros->{'fr/DDD'}[0]->etat);

        $this->assertInternalType('array', $objectResponse->numeros->{'fr/MP'});
        $this->assertEquals('300', $objectResponse->numeros->{'fr/MP'}[0]->numero);
        $this->assertEquals('bon', $objectResponse->numeros->{'fr/MP'}[0]->etat);
        $this->assertEquals('301', $objectResponse->numeros->{'fr/MP'}[1]->numero);
        $this->assertEquals('mauvais', $objectResponse->numeros->{'fr/MP'}[1]->etat);
    }
}
