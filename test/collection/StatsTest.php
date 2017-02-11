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
        $this->assertEquals(3, $objectResponse->CB->storycount);
        $this->assertEquals(2, $objectResponse->CB->missingstorycount);
    }

    public function testGetSuggestions() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues', TestCommon::$dmUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());
        $this->assertInternalType('object', $objectResponse);

        $this->assertEquals(4, $objectResponse->minScore);
        $this->assertEquals(4, $objectResponse->maxScore);

        $this->assertEquals(1, count($objectResponse->stories));

        $issue1 = $objectResponse->stories->{'us/CBL 7'};
        $this->assertEquals(4, $issue1->score);

        $this->assertEquals('ARC CBL 5B', array_keys(get_object_vars($issue1->stories))[0]);
        $story1 = $issue1->stories->{'ARC CBL 5B'};
        $this->assertEquals('CB', $story1->author->personcode);
        $this->assertEquals('Carl Barks', $story1->author->fullname);
        $this->assertEquals('Title of story ARC CBL 5B', $story1->story->title);
        $this->assertEquals('Comment of story ARC CBL 5B', $story1->story->storycomment);

        $story2 = $issue1->stories->{'W WDC  32-02'};
        $this->assertEquals('CB', $story2->author->personcode);
        $this->assertEquals('Carl Barks', $story2->author->fullname);
        $this->assertEquals('Title of story W WDC  32-02', $story2->story->title);
        $this->assertEquals('Comment of story W WDC  32-02', $story2->story->storycomment);
    }
}
