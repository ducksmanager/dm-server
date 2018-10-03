<?php
namespace DmServer\Test;

use DmServer\DmServer;
use DmServer\SimilarImagesHelper;
use Symfony\Component\HttpFoundation\Response;

class StatusTest extends TestCommon
{
    private function getCoverIdStatusForMockedResults($url, $mockedResults) {
        $user = self::createTestCollection();
        self::setSessionUser($this->app, $user);

        SimilarImagesHelper::$mockedResults = $mockedResults;

        return $this->buildAuthenticatedService($url, self::$dmUser, [], [], 'GET')->call();
    }

    public function testGetCoverIdStatus() {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec',
            json_encode([
                'image_ids' => [1,2,3],
                'type' => 'INDEX_IMAGE_IDS'
            ])
        );

        $this->assertEquals('Pastec OK with 3 images indexed', $this->getResponseContent($response));
    }

    public function testGetCoverIdStatusInvalidHost() {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec/invalidpastechost',
            json_encode([])
        );

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals('Invalid Pastec host : invalidpastechost', $response->getContent());
        });
    }

    public function testGetCoverIdStatusNoCoverData() {
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

    public function testGetCoverIdStatusInvalidCoverData() {
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

    public function testGetCoverIdStatusUnreachable() {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastec',
            json_encode(null)
        );

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals('Pastec is unreachable', $response->getContent());
        });
    }

    public function testGetImageSearchStatus() {
        $response = $this->getCoverIdStatusForMockedResults(
            '/status/pastecsearch',
            json_encode([
                'imageIds' => [1,2,3],
                'type' => 'INDEX_IMAGE_IDS'
            ])
        );

        $this->assertEquals('Pastec OK with 3 images indexed', $this->getResponseContent($response));
    }

    public function testGetDbStatus() {
        $user = self::createTestCollection();
        self::setSessionUser($this->app, $user);
        self::createCoaData();
        self::createCoverIds();
        self::createStatsData($user->getId());
        self::createEdgeCreatorData($user->getId());

        $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('OK for all databases', $this->getResponseContent($response));
    }

    public function testGetDbStatusMissingCoaData() {
        $user = self::createTestCollection();
        self::setSessionUser($this->app, $user);
        self::createCoverIds();
        self::createStatsData($user->getId());

        $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertContains('Error for db_coa', $response->getContent());
        });
    }

    public function testGetDbStatusDBDown() {
        unset(DmServer::$entityManagers[DmServer::CONFIG_DB_KEY_COA]);

        try {
            $response = $this->buildAuthenticatedService('/status/db', self::$dmUser, [], [], 'GET')->call();
            $this->assertUnsuccessfulResponse($response, function(Response $response) {
                $this->assertContains('Error for db_coa : JSON cannot be decoded', $response->getContent());
            });
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
