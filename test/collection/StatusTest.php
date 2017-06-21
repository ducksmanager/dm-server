<?php
namespace DmServer\Test;

use DmServer\SimilarImagesHelper;

class StatusTest extends TestCommon
{

    public function testGetStatus() {
        self::createCoaData();
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createCoverIds();
        self::createStatsData();
        self::createEdgeCreatorData();

        SimilarImagesHelper::$mockedResults = json_encode(['image_ids' => [1,2,3], 'type' => 'INDEX_IMAGE_IDS']);

        $response = $this->buildAuthenticatedService('/status', TestCommon::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('OK for all databases<br />Pastec OK with 3 images indexed', $response->getContent());
    }

    public function testGetStatusMissingCoaData() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createCoverIds();
        self::createStatsData();

        $response = $this->buildAuthenticatedService('/status', TestCommon::$dmUser, [], [], 'GET')->call();

        $this->assertContains('Error for db_coa', $response->getContent());
    }
}
