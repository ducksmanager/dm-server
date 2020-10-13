<?php
namespace App\Tests\Controller;

use App\Entity\Coverid\Covers;
use App\Tests\Fixtures\CoaEntryFixture;
use App\Tests\Fixtures\CoaFixture;
use App\Tests\TestCommon;
use Symfony\Component\HttpFoundation\Response;

class CoaListsTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['coa'];
    }

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([ CoaFixture::class, CoaEntryFixture::class ], true, 'coa');
    }

    public function testGetCountryList(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries/fr', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('Espagne', $objectResponse->es);
        $this->assertEquals('USA', $objectResponse->us);
        $this->assertCount(3, (array)$objectResponse);
    }

    public function testGetCountryListFromCountryCodes(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries/fr/fr,us', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('France', $objectResponse->fr);
        $this->assertEquals('USA', $objectResponse->us);
        $this->assertCount(2, (array)$objectResponse);
    }

    public function testGetCountryListFromCountryCodesOtherLocale(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/countries/es/fr,us', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Francia', $objectResponse->fr);
        $this->assertEquals('EE.UU.', $objectResponse->us);
        $this->assertCount(2, (array)$objectResponse);
    }

    public function testGetPublicationListAll(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('object', $objectResponse);
        $this->assertCount(4, get_object_vars($objectResponse));
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->{'fr/MP'});
        $this->assertEquals('Picsou Magazine', $objectResponse->{'fr/PM'});
        $this->assertEquals('Carl Barks Library', $objectResponse->{'us/CBL'});
    }

    public function testGetPublicationListFromCountry(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('object', $objectResponse);
        $this->assertCount(3, get_object_vars($objectResponse));
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->{'fr/MP'});
        $this->assertEquals('Picsou Magazine', $objectResponse->{'fr/PM'});
    }

    public function testGetPublicationListFromPublicationCodes(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr/DDD,us/CBL', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('Dynastie', $objectResponse->{'fr/DDD'});
        $this->assertEquals('Carl Barks Library', $objectResponse->{'us/CBL'});
    }

    public function testGetPublicationListInvalidCountry(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/publications/fr0', self::$dmUser)->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCountIssuesPerPublication(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/count', self::$dmUser)->call();

        $arrayResponse = json_decode($this->getResponseContent($response), true);

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals([
            'fr/DDD' => 3,
            'fr/MP' => 1,
            'fr/PM' => 2,
            'us/CBL' => 1,
            'de/MM' => 1,
            'fr/CB' => 1,
        ], $arrayResponse);
    }

    public function testGetIssueListPerPublicationCode(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DDD', self::$dmUser)->call();

        $arrayResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals(['0', '1', '2'], $arrayResponse);
    }

    public function testGetIssueListWithSpaces(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/CB', self::$dmUser)->call();

        $arrayResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals('PN 1', $arrayResponse[0]);
    }

    public function testGetIssueListWithTitles(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/withTitle/fr/DDD', self::$dmUser)->call();

        $this->assertJsonStringEqualsJsonString(json_encode([
            '0' => 'Volume 0',
            '1' => 'Volume 1',
            '2' => null
        ]), $this->getResponseContent($response));
    }

    public function testGetIssueListEmptyList(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD', self::$dmUser)->call();

        $arrayResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('array', $arrayResponse);
        $this->assertCount(0, $arrayResponse);
    }

    public function testGetIssueListInvalidPublicationCode(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issues/fr/DD_', self::$dmUser)->call();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetIssueListByIssueCodes(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issuesbycodes/fr/DDD 1', self::$dmUser)->call();

        $arrayResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('object', $arrayResponse);

        $this->assertInternalType('object', $arrayResponse->{'fr/DDD 1'});
        $this->assertEquals('fr', $arrayResponse->{'fr/DDD 1'}->countrycode);
        $this->assertEquals('Dynastie', $arrayResponse->{'fr/DDD 1'}->publicationtitle);
        $this->assertEquals('1', $arrayResponse->{'fr/DDD 1'}->issuenumber);
    }

    public function testGetIssueListByIssueCodesNoCoaIssue(): void
    {
        $this->loadFixtures([ ], false, 'coverid');
        $coveridEm = $this->getEm('coverid');
        $coveridEm->persist(
            $cover = (new Covers())
                ->setIssuecode('fr/DDDDD 1')
                ->setSitecode('webusers')
                ->setUrl('abc.jpg')
        );
        $coveridEm->flush();

        $response = $this->buildAuthenticatedServiceWithTestUser('/coa/list/issuesbycodes/fr/DDDDD 1', self::$dmUser)->call();

        $arrayResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals([], $arrayResponse);
    }
}
