<?php
namespace DmServer\Test;

use Coverid\Models\Covers;
use DmServer\DmServer;
use Doctrine\ORM\ORMException;
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
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries/fr', self::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('Espagne', $objectResponse->es);
        $this->assertEquals('USA', $objectResponse->us);
        $this->assertCount(3, (array)$objectResponse);
    }

    public function testGetCountryListFromCountryCodes() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries/fr/fr,us', self::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('USA', $objectResponse->us);
        $this->assertCount(2, (array)$objectResponse);
    }

    public function testGetCountryListFromCountryCodesOtherLocale() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries/es/fr,us', self::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Francia', $objectResponse->fr);
        $this->assertEquals('EE.UU.', $objectResponse->us);
        $this->assertCount(2, (array)$objectResponse);
    }

    public function testGetPublicationListFromCountry() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr', self::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->{'fr/MP'});
    }

    public function testGetPublicationListFromPublicationCodes() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr/DDD,us/CBL', self::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Carl Barks Library', $objectResponse->{'us/CBL'});
    }

    public function testGetPublicationListInvalidCountry() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr0', self::$dmUser)->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetIssueList() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DDD', self::$dmUser)->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals('1', $arrayResponse[0]);
        $this->assertEquals('2', $arrayResponse[1]);
    }

    public function testGetIssueListEmptyList() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD', self::$dmUser)->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertCount(0, $arrayResponse);
    }

    public function testGetIssueListInvalidPublicationCode() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD_', self::$dmUser)->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetIssueListByIssueCodes() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issuesbycodes/fr/DDD 1', self::$dmUser)->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $arrayResponse);

        $this->assertInternalType('object', $arrayResponse->{'fr/DDD 1'});
        $this->assertEquals('fr', $arrayResponse->{'fr/DDD 1'}->countrycode);
        $this->assertEquals('Dynastie', $arrayResponse->{'fr/DDD 1'}->publicationtitle);
        $this->assertEquals('1', $arrayResponse->{'fr/DDD 1'}->issuenumber);
    }

    public function testGetIssueListByIssueCodesNoCoaIssue() {
        try {
            DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COVER_ID]->persist(
                $cover = (new Covers())
                    ->setIssuecode('fr/DDDDD 1')
                    ->setSitecode('webusers')
                    ->setUrl('abc.jpg')
            );
            DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COVER_ID]->flush();
        }
        catch (ORMException $e) {
            $this->fail("Failed to create cover : {$e->getMessage()}");
        }

        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issuesbycodes/fr/DDDDD 1', self::$dmUser)->call();

        $arrayResponse = json_decode($response->getContent());
        $this->assertEquals([], $arrayResponse);
    }
}
