<?php
namespace DmServer\Test;

class StatusTest extends TestCommon
{

    public function testGetStatus() {
        self::createCoaData();
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createCoverIds();
        self::createStatsData();
        self::createEdgeCreatorData();

        $response = $this->buildAuthenticatedService('/status', TestCommon::$dmUser, [], [], 'GET')->call();

        $this->assertEquals('OK for all databases', $response->getContent());
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
