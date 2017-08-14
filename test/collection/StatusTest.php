<?php
namespace DmServer\Test;

use DmServer\DmServer;
use DmServer\SimilarImagesHelper;

class StatusTest extends TestCommon
{
    public function testGetStatus() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createCoverIds();
        self::createCoaData();
        self::createStatsData();
        self::createEdgeCreatorData();

        SimilarImagesHelper::$mockedResults = json_encode(['image_ids' => [1,2,3], 'type' => 'INDEX_IMAGE_IDS']);

        $response = $this->buildAuthenticatedService('/status', TestCommon::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('OK for all databases<br />Pastec OK with 3 images indexed', $response->getContent());
    }

    public function testGetStatusNoCoverData() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createCoverIds();
        self::createCoaData();
        self::createStatsData();
        self::createEdgeCreatorData();

        SimilarImagesHelper::$mockedResults = json_encode(['image_ids' => [], 'type' => 'INDEX_IMAGE_IDS']);

        $response = $this->buildAuthenticatedService('/status', TestCommon::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('OK for all databases<br /><b>Pastec has no images indexed</b>', $response->getContent());
    }

    public function testGetStatusMissingCoaData() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createCoverIds();
        self::createStatsData();

        $response = $this->buildAuthenticatedService('/status', TestCommon::$dmUser, [], [], 'GET')->call();

        $this->assertContains('Error for db_coa', $response->getContent());
    }

    public function testGetStatusDBDown() {
        unset(DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COA]);

        try {
            $response = $this->buildAuthenticatedService('/status', TestCommon::$dmUser, [], [], 'GET')->call();
            $this->assertContains('Error for db_coa : JSON cannot be decoded', $response->getContent());
        }
        finally {
            self::recreateSchema(DmServer::CONFIG_DB_KEY_COA);
        }
    }
}
