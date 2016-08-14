<?php
namespace Wtd\Test;

class CoaListsTest extends TestCommon
{
    public function setUp()
    {
        parent::setUp();
        self::createTestCollection();
        self::createCoaData();
    }

    public function testGetCountryList() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries', 'GET');
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('Espagne', $objectResponse->es);
    }

    public function testGetPublicationList() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr', 'GET');
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->{'fr/MP'});
    }
}
