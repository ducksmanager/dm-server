<?php
namespace DmServer\Test;

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
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries', TestCommon::$dmUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('Espagne', $objectResponse->es);
    }

    public function testGetCountryListFromCountryCodes() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries/fr,us', TestCommon::$dmUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('USA', $objectResponse->us);
    }

    public function testGetPublicationListFromCountry() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr', TestCommon::$dmUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->{'fr/MP'});
    }

    public function testGetPublicationListFromPublicationCodes() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr/DDD,us/CBL', TestCommon::$dmUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Carl Barks Library', $objectResponse->{'us/CBL'});
    }

    public function testGetPublicationListInvalidCountry() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr0', TestCommon::$dmUser);
        $response = $service->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetIssueList() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DDD', TestCommon::$dmUser);
        $response = $service->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals('1', $arrayResponse[0]);
        $this->assertEquals('2', $arrayResponse[1]);
    }

    public function testGetIssueListEmptyList() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD', TestCommon::$dmUser);
        $response = $service->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals(0, count($arrayResponse));
    }

    public function testGetIssueListInvalidPublicationCode() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD_', TestCommon::$dmUser);
        $response = $service->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetIssueListByIssueCodes() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issuesbycodes/fr/DDD 1', TestCommon::$dmUser);
        $response = $service->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $arrayResponse);

        $this->assertInternalType('object', $arrayResponse->{'fr/DDD 1'});
        $this->assertEquals('fr', $arrayResponse->{'fr/DDD 1'}->countrycode);
        $this->assertEquals('Dynastie', $arrayResponse->{'fr/DDD 1'}->publicationtitle);
        $this->assertEquals('1', $arrayResponse->{'fr/DDD 1'}->issuenumber);
    }
}
