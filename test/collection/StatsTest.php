<?php
namespace DmServer\Test;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use DmServer\CoverIdController;

class StatsTest extends TestCommon
{

    public function setUp()
    {
        parent::setUp();
        self::createCoaData();
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createStatsData();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testGetWatchedAuthors() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/stats/watchedauthors', TestCommon::$testUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());
        $this->assertInternalType('array', $objectResponse);
        $this->assertEquals('CB', $objectResponse[0]);
    }
}
