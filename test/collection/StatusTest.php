<?php
namespace DmServer\Test;

use DmServer\DmServer;
use DmServer\SimilarImagesHelper;
use Symfony\Component\HttpFoundation\Response;

class StatusTest extends TestCommon
{
    public function testGetCoverIdStatus() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        SimilarImagesHelper::$mockedResults = json_encode(['image_ids' => [1,2,3], 'type' => 'INDEX_IMAGE_IDS']);

        $response = $this->buildAuthenticatedService('/status/pastec', self::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('Pastec OK with 3 images indexed', $response->getContent());
    }

    public function testGetCoverIdStatusNoCoverData() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        SimilarImagesHelper::$mockedResults = json_encode(['image_ids' => [], 'type' => 'INDEX_IMAGE_IDS']);

        $response = $this->buildAuthenticatedService('/status/pastec', self::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('<b>Pastec has no images indexed</b>', $response->getContent());
    }

    public function testGetDbStatus() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createCoaData();
        self::createCoverIds();
        self::createStatsData(self::getSessionUser($this->app)['id']);
        self::createEdgeCreatorData(self::getSessionUser($this->app)['id']);

        $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('OK for all databases', $response->getContent());
    }

    public function testGetDbStatusMissingCoaData() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createCoverIds();
        self::createStatsData(self::getSessionUser($this->app)['id']);

        $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();

        $this->assertContains('Error for db_coa', $response->getContent());
    }

    public function testGetDbStatusDBDown() {
        unset(DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COA]);

        try {
            $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();
            $this->assertContains('Error for db_coa : JSON cannot be decoded', $response->getContent());
        }
        finally {
            self::recreateSchema(DmServer::CONFIG_DB_KEY_COA);
        }
    }

    public function testGetSwaggerJson() {
        $response = $this->buildAuthenticatedService('/status/swagger.json', self::$dmUser, [], [], 'GET')->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetSwaggerJsonNotExisting() {
        DmServer::$settings['swagger_path'] = '/not/existing';
        $response = $this->buildAuthenticatedService('/status/swagger.json', self::$dmUser, [], [], 'GET')->call();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
