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
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries', TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('Espagne', $objectResponse->es);
    }

    public function testGetCountryListFromCountryCodes() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries/fr,us', TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('USA', $objectResponse->us);
    }

    public function testGetPublicationListFromCountry() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr', TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->{'fr/MP'});
    }

    public function testGetPublicationListFromPublicationCodes() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr/DDD,us/CBL', TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Carl Barks Library', $objectResponse->{'us/CBL'});
    }

    public function testGetPublicationListInvalidCountry() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr0', TestCommon::$dmUser)->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetIssueList() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DDD', TestCommon::$dmUser)->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals('1', $arrayResponse[0]);
        $this->assertEquals('2', $arrayResponse[1]);
    }

    public function testGetIssueListEmptyList() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD', TestCommon::$dmUser)->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals(0, count($arrayResponse));
    }

    public function testGetIssueListInvalidPublicationCode() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD_', TestCommon::$dmUser)->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetIssueListByIssueCodes() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issuesbycodes/fr/DDD 1', TestCommon::$dmUser)->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $arrayResponse);

        $this->assertInternalType('object', $arrayResponse->{'fr/DDD 1'});
        $this->assertEquals('fr', $arrayResponse->{'fr/DDD 1'}->countrycode);
        $this->assertEquals('Dynastie', $arrayResponse->{'fr/DDD 1'}->publicationtitle);
        $this->assertEquals('1', $arrayResponse->{'fr/DDD 1'}->issuenumber);
    }
}
