<?php
namespace App\Tests;

use App\Helper\SimilarImagesHelper;
use App\Tests\Controller\CoverIdTest;
use App\Tests\Fixtures\CoaEntryFixture;
use App\Tests\Fixtures\CoaFixture;
use App\Tests\Fixtures\CoverIdFixture;
use App\Tests\Fixtures\DmStatsFixture;
use App\Tests\Fixtures\EdgeCreatorFixture;
use Symfony\Component\HttpFoundation\Response;

class StatusTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm', 'coa', 'coverid', 'edgecreator', 'dm_stats'];
    }

    private function getCoverIdStatusForMockedResults($url, $mockedResults): Response {
        $this->createUserCollection(self::$defaultTestDmUserName);
        SimilarImagesHelper::$mockedResults = $mockedResults;

        return $this->buildAuthenticatedService($url, self::$dmUser, [], [], 'GET')->call();
    }

    public function testGetCoverIdStatus(): void {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec',
            json_encode([
                'image_ids' => [1,2,3],
                'type' => 'INDEX_IMAGE_IDS'
            ])
        );

        $this->assertEquals('Pastec OK with 3 images indexed', $this->getResponseContent($response));
    }

    public function testGetCoverIdStatusInvalidHost(): void {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec/invalidpastechost',
            json_encode([])
        );

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals('Invalid Pastec host : invalidpastechost', $response->getContent());
        });
    }

    public function testGetCoverIdStatusNoCoverData(): void {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec',
            json_encode([
                'image_ids' => [],
                'type' => 'INDEX_IMAGE_IDS'
            ])
        );

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals('Pastec has no images indexed', $response->getContent());
        });
    }

    public function testGetCoverIdStatusInvalidCoverData(): void {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec',
            json_encode([
                'image_ids' => [],
                'type' => 'INVALID_TYPE'
            ])
        );

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals('Invalid return type : INVALID_TYPE', $response->getContent());
        });
    }

    public function testGetCoverIdStatusUnreachable(): void {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec',
            json_encode(null)
        );

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals('Pastec is unreachable', $response->getContent());
        });
    }

    public function testGetImageSearchStatus(): void {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastecsearch',
            json_encode(CoverIdTest::$coverSearchResultsSimple)
        );

        $this->assertEquals('Pastec search returned 1 image(s)', $this->getResponseContent($response));
    }

    public function testGetDbStatus(): void {
        $this->createUserCollection(self::$defaultTestDmUserName);
        $this->loadFixtures([ CoaFixture::class, CoaEntryFixture::class ], true, 'coa');
        $urls = [
            'fr/DDD 1' => '2010/12/fr_ddd_001a_001.jpg',
            'fr/DDD 2' => '2010/12/fr_ddd_002a_001.jpg',
            'fr/MP 300' => '2010/12/fr_mp_0300a_001.jpg',
            'fr/XXX 111' => '2010/12/fr_xxx_111_001.jpg'
        ];

        CoverIdFixture::$urls = $urls;
        $this->loadFixtures([CoverIdFixture::class], true, 'coverid');

        EdgeCreatorFixture::$user = $this->getUser(self::$defaultTestDmUserName);
        $this->loadFixtures([ EdgeCreatorFixture::class ], true, 'edgecreator');

        DmStatsFixture::$userId = 1;
        $this->loadFixtures([ DmStatsFixture::class ], true, 'dm_stats');

        $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('OK for all databases', $this->getResponseContent($response));
    }

    public function testGetDbStatusMissingCoaData(): void {
        $this->createUserCollection(self::$defaultTestDmUserName);
        $urls = [
            'fr/DDD 1' => '2010/12/fr_ddd_001a_001.jpg',
            'fr/DDD 2' => '2010/12/fr_ddd_002a_001.jpg',
            'fr/MP 300' => '2010/12/fr_mp_0300a_001.jpg',
            'fr/XXX 111' => '2010/12/fr_xxx_111_001.jpg'
        ];

        CoverIdFixture::$urls = $urls;
        $this->loadFixtures([CoverIdFixture::class], true, 'coverid');

        EdgeCreatorFixture::$user = $this->getUser(self::$defaultTestDmUserName);
        $this->loadFixtures([ EdgeCreatorFixture::class ], true, 'edgecreator');

        DmStatsFixture::$userId = 1;
        $this->loadFixtures([ DmStatsFixture::class ], true, 'dm_stats');

        $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertContains('Error for coa : received response []', $response->getContent());
        });
    }
}
