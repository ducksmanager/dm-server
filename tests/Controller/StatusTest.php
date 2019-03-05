<?php
namespace App\Tests;

use App\Helper\SimilarImagesHelper;
use App\Tests\Controller\CoverIdTest;
use App\Tests\Fixtures\CoverIdFixture;
use App\Tests\Fixtures\DmStatsFixture;
use App\Tests\Fixtures\EdgeCreatorFixture;
use Symfony\Component\HttpFoundation\Response;

class StatusTest extends TestCommon
{
    private function getCoverIdStatusForMockedResults($url, $mockedResults): Response {
        $this->createUserCollection('dm_test_user');
        SimilarImagesHelper::$mockedResults = $mockedResults;

        return $this->buildAuthenticatedService($url, self::$dmUser, [], [], 'GET')->call();
    }

    public function testGetCoverIdStatus(): void {
        $this->spinUp('dm');
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
        $this->spinUp('dm');
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec/invalidpastechost',
            json_encode([])
        );

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals('Invalid Pastec host : invalidpastechost', $response->getContent());
        });
    }

    public function testGetCoverIdStatusNoCoverData(): void {
        $this->spinUp('dm');
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
        $this->spinUp('dm');
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
        $this->spinUp('dm');
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec',
            json_encode(null)
        );

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals('Pastec is unreachable', $response->getContent());
        });
    }

    public function testGetImageSearchStatus(): void {
        $this->spinUp('dm');
        $this->spinUp('co');
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastecsearch',
            json_encode(CoverIdTest::$coverSearchResultsSimple)
        );

        $this->assertEquals('Pastec search returned 1 image(s)', $this->getResponseContent($response));
    }

    public function testGetDbStatus(): void {
        $this->spinUp('dm');
        $this->spinUp('coa');
        $this->spinUp('coverid');
        $this->spinUp('dm_stats');
        $this->spinUp('edgecreator');

        $this->createUserCollection('dm_test_user');
        self::runCommand('doctrine:fixtures:load -q -n --em=coa --group=coa');
        $urls = [
            'fr/DDD 1' => '2010/12/fr_ddd_001a_001.jpg',
            'fr/DDD 2' => '2010/12/fr_ddd_002a_001.jpg',
            'fr/MP 300' => '2010/12/fr_mp_0300a_001.jpg',
            'fr/XXX 111' => '2010/12/fr_xxx_111_001.jpg'
        ];
        foreach($urls as $issueNumber => $url) {
            $this->loadFixture('coverid', new CoverIdFixture($issueNumber, $url));
        }
        $this->loadFixture('edgecreator', new EdgeCreatorFixture($this->getUser('dm_test_user')));
        $this->loadFixture('dm_stats', new DmStatsFixture(1));

        $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('OK for all databases', $this->getResponseContent($response));
    }

    public function testGetDbStatusMissingCoaData(): void {
        $this->spinUp('dm');
        $this->spinUp('coa');
        $this->spinUp('coverid');
        $this->spinUp('dm_stats');
        $this->spinUp('edgecreator');
        $this->createUserCollection('dm_test_user');
        $urls = [
            'fr/DDD 1' => '2010/12/fr_ddd_001a_001.jpg',
            'fr/DDD 2' => '2010/12/fr_ddd_002a_001.jpg',
            'fr/MP 300' => '2010/12/fr_mp_0300a_001.jpg',
            'fr/XXX 111' => '2010/12/fr_xxx_111_001.jpg'
        ];
        foreach($urls as $issueNumber => $url) {
            $this->loadFixture('coverid', new CoverIdFixture($issueNumber, $url));
        }
        $this->loadFixture('edgecreator', new EdgeCreatorFixture($this->getUser('dm_test_user')));
        $this->loadFixture('dm_stats', new DmStatsFixture(1));

        $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertContains('Error for coa : received response []', $response->getContent());
        });
    }
}
