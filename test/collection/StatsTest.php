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
        $this->createStatsData();
    }

    public function testGetWatchedAuthors() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/watchedauthorsstorycount', TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());
        $this->assertInternalType('object', $objectResponse);
        $this->assertCount(2, get_object_vars($objectResponse));
        $this->assertEquals('CB', array_keys(get_object_vars($objectResponse))[0]);
        $this->assertEquals('Carl Barks', $objectResponse->CB->fullname);
        $this->assertEquals(4, $objectResponse->CB->storycount);
        $this->assertEquals(3, $objectResponse->CB->missingstorycount);

        $this->assertEquals('DR', array_keys(get_object_vars($objectResponse))[1]);
        $this->assertEquals('Don Rosa', $objectResponse->DR->fullname);
        $this->assertEquals(1, $objectResponse->DR->storycount);
        $this->assertEquals(1, $objectResponse->DR->missingstorycount);
    }

    public function testGetSuggestions() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues', TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());
        $this->assertInternalType('object', $objectResponse);

        $this->assertEquals(2, $objectResponse->minScore);
        $this->assertEquals(6, $objectResponse->maxScore); // fr/PM 315 : 1xDR + 1xCB = 1x4 + 1x2

        $this->assertCount(3, get_object_vars($objectResponse->issues));

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

        $issue3 = $objectResponse->issues->{'fr/PM 315'};
        $this->assertEquals(6, $issue3->score);
        $this->assertEquals('fr/PM', $issue3->publicationcode);
        $this->assertEquals('315', $issue3->issuenumber);

        $story3 = 'AR 201';
        $this->assertEquals($story3, $issue3->stories->DR[0]);

        $story4 = 'W WDC 130-02';
        $this->assertEquals($story4, $issue3->stories->CB[0]);

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
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues/fr', TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());
        $this->assertInternalType('object', $objectResponse);
        $this->assertCount(2, get_object_vars($objectResponse->issues));

        $issue1 = $objectResponse->issues->{'fr/DDD 1'};
        $this->assertEquals(2, $issue1->score);
        $this->assertEquals('fr/DDD', $issue1->publicationcode);
        $this->assertEquals('1', $issue1->issuenumber);

        $story1 = 'W WDC  32-02';
        $this->assertEquals($story1, $issue1->stories->CB[0]);
    }
}
