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

        $this->assertEquals(2, $objectResponse->minScore);
        $this->assertEquals(4, $objectResponse->maxScore);

        $this->assertEquals(2, count(get_object_vars($objectResponse->issues)));

        $issue1 = $objectResponse->issues->{'us/CBL 7'};
        $this->assertEquals(4, $issue1->score);
        $this->assertEquals('us/CBL', $issue1->publicationcode);
        $this->assertEquals('7', $issue1->issuenumber);

        $story1 = 'ARC CBL 5B';
        $this->assertEquals($story1, $issue1->stories->CB[0]);

        $story2 = 'W WDC  32-02';
        $this->assertEquals($story2, $issue1->stories->CB[1]);


        $issue2 = $objectResponse->issues->{'fr/DDD 1'};
        $this->assertEquals(2, $issue2->score);
        $this->assertEquals('fr/DDD', $issue2->publicationcode);
        $this->assertEquals('1', $issue2->issuenumber);

        $this->assertEquals($story2, $issue2->stories->CB[0]);

        // Story details assertions

        $this->assertEquals('CB', $objectResponse->storyDetails->$story1->personcode);
        $this->assertEquals('Title of story ARC CBL 5B', $objectResponse->storyDetails->$story1->title);
        $this->assertEquals('Comment of story ARC CBL 5B', $objectResponse->storyDetails->$story1->storycomment);

        $this->assertEquals('CB', $objectResponse->storyDetails->$story2->personcode);
        $this->assertEquals('Title of story W WDC  32-02', $objectResponse->storyDetails->$story2->title);
        $this->assertEquals('Comment of story W WDC  32-02', $objectResponse->storyDetails->$story2->storycomment);

        // Author details assertions

        $this->assertEquals('Carl Barks', $objectResponse->authors->CB);
    }

    public function testGetSuggestionsCountryFilter() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues/fr', TestCommon::$dmUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());
        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals(1, count(get_object_vars($objectResponse->issues)));

        $issue1 = $objectResponse->issues->{'fr/DDD 1'};
        $this->assertEquals(2, $issue1->score);
        $this->assertEquals('fr/DDD', $issue1->publicationcode);
        $this->assertEquals('1', $issue1->issuenumber);

        $story1 = 'W WDC  32-02';
        $this->assertEquals($story1, $issue1->stories->CB[0]);
    }
}
