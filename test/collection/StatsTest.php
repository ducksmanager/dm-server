<?php
namespace DmServer\Test;

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

    public function testGetWatchedAuthors() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/watchedauthorsstorycount', TestCommon::$dmUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());
        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals('CB', array_keys(get_object_vars($objectResponse))[0]);
        $this->assertEquals('Carl Barks', $objectResponse->CB->fullname);
        $this->assertEquals(2, $objectResponse->CB->storycount);
        $this->assertEquals(1, $objectResponse->CB->missingstorycount);
    }

    public function testGetSuggestions() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedpublications', TestCommon::$dmUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());
    }
}
