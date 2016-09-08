<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;

class CoaListsTest extends TestCommon
{
    public function setUp()
    {
        parent::setUp();
        self::createTestCollection();
        self::createCoaData();
    }

    public function testGetCountryList() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries', TestCommon::$testUser, 'GET');
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('Espagne', $objectResponse->es);
    }

    public function testGetPublicationList() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr', TestCommon::$testUser, 'GET');
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->{'fr/MP'});
    }

    public function testGetPublicationListInvalidCountry() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr0', TestCommon::$testUser, 'GET');
        $response = $service->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetIssueList() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DDD', TestCommon::$testUser, 'GET');
        $response = $service->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals('1', $arrayResponse[0]);
        $this->assertEquals('2', $arrayResponse[1]);
    }

    public function testGetIssueListEmptyList() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD', TestCommon::$testUser, 'GET');
        $response = $service->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals(0, count($arrayResponse));
    }

    public function testGetIssueListInvalidPublicationCode() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD_', TestCommon::$testUser, 'GET');
        $response = $service->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
