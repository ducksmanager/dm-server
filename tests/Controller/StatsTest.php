<?php
namespace App\Tests;

use App\Tests\Fixtures\CoaEntryFixture;
use App\Tests\Fixtures\CoaFixture;
use App\Tests\Fixtures\DmStatsFixture;

class StatsTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm', 'coa', 'dm_stats'];
    }

    public function setUp()
    {
        parent::setUp();
        $this->createUserCollection(self::$defaultTestDmUserName);
        $this->loadFixtures([ CoaFixture::class, CoaEntryFixture::class ], true, 'coa');

        DmStatsFixture::$userId = 1;
        $this->loadFixtures([ DmStatsFixture::class ], true, 'dm_stats');
    }

    public function testGetWatchedAuthors(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/watchedauthorsstorycount', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals((object)[
            'CB' => (object)[
                'fullname' => 'Carl Barks',
                'missingstorycount' => 3,
                'storycount' => 4,
            ],
            'DR' => (object)[
                'fullname' => 'Don Rosa',
                'missingstorycount' => 1,
                'storycount' => 1,
            ],
        ], $objectResponse);
    }

    public function testGetSuggestions(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals((object)[
            'maxScore' => 6,
            'minScore' => 2,
            'issues' => (object)[
                'fr/PM 315' => (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC 130-02'],
                        'DR' => ['AR 201'],
                    ],
                    'score' => 6,
                    'publicationcode' => 'fr/PM',
                    'issuenumber' => '315',
                ],
                'us/CBL 7' => (object)[
                    'stories' => (object)[
                        'CB' => [
                            'ARC CBL 5B',
                            'W WDC  32-02',
                        ],
                    ],
                    'score' => 4,
                    'publicationcode' => 'us/CBL',
                    'issuenumber' => '7',
                ],
                'fr/DDD 1' => (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC  32-02'],
                    ],
                    'score' => 2,
                    'publicationcode' => 'fr/DDD',
                    'issuenumber' => '1',
                ],
            ],
            'authors' => (object)[
                'CB' => 'Carl Barks',
                'DR' => 'Don Rosa',
            ],
            'publicationTitles' => (object)[
                'us/CBL' => 'Carl Barks Library',
                'fr/DDD' => 'Dynastie',
                'fr/PM' => 'Picsou Magazine',
            ],
            'storyDetails' => (object)[
                'AR 201' => (object)[
                    'storycomment' => 'Comment of story AR 201',
                    'title' => 'Title of story AR 201',
                    'personcode' => 'DR',
                ],
                'ARC CBL 5B' => (object)[
                    'storycomment' => 'Comment of story ARC CBL 5B',
                    'title' => 'Title of story ARC CBL 5B',
                    'personcode' => 'CB',
                ],
                'W WDC  32-02' => (object)[
                    'storycomment' => 'Comment of story W WDC  32-02',
                    'title' => 'Title of story W WDC  32-02',
                    'personcode' => 'CB',
                ],
                'W WDC 130-02' => (object)[
                    'storycomment' => 'Comment of story W WDC 130-02',
                    'title' => 'Title of story W WDC 130-02',
                    'personcode' => 'CB',
                ],
            ],
        ], $objectResponse);
        $this->assertInternalType('object', $objectResponse);
    }

    public function testGetSuggestionsByCountry(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues/fr', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals((object)[
            'maxScore' => 6,
            'minScore' => 2,
            'issues' => (object)[
                'fr/PM 315' => (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC 130-02'],
                        'DR' => ['AR 201'],
                    ],
                    'score' => 6,
                    'publicationcode' => 'fr/PM',
                    'issuenumber' => '315',
                ],
                'fr/DDD 1' => (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC  32-02'],
                    ],
                    'score' => 2,
                    'publicationcode' => 'fr/DDD',
                    'issuenumber' => '1',
                ],
            ],
            'authors' => (object)[
                'CB' => 'Carl Barks',
                'DR' => 'Don Rosa',
            ],
            'publicationTitles' => (object)[
                'fr/DDD' => 'Dynastie',
                'fr/PM' => 'Picsou Magazine',
            ],
            'storyDetails' => (object)[
                'AR 201' => (object)[
                    'storycomment' => 'Comment of story AR 201',
                    'title' => 'Title of story AR 201',
                    'personcode' => 'DR',
                ],
                'W WDC  32-02' => (object)[
                    'storycomment' => 'Comment of story W WDC  32-02',
                    'title' => 'Title of story W WDC  32-02',
                    'personcode' => 'CB',
                ],
                'W WDC 130-02' => (object)[
                    'storycomment' => 'Comment of story W WDC 130-02',
                    'title' => 'Title of story W WDC 130-02',
                    'personcode' => 'CB',
                ],
            ],
        ], $objectResponse);
        $this->assertInternalType('object', $objectResponse);
        $this->assertCount(2, get_object_vars($objectResponse->issues));

        $issue1 = $objectResponse->issues->{'fr/DDD 1'};
        $this->assertEquals(2, $issue1->score);
        $this->assertEquals('fr/DDD', $issue1->publicationcode);
        $this->assertEquals('1', $issue1->issuenumber);

        $this->assertEquals('W WDC  32-02', $issue1->stories->CB[0]);
    }

    public function testGetSuggestionsSincePreviousVisit(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues/ALL/since_previous_visit', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals((object)[
            'maxScore' => 6,
            'minScore' => 6,
            'issues' => (object)[
                'fr/PM 315' => (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC 130-02'],
                        'DR' => ['AR 201'],
                    ],
                    'score' => 6,
                    'publicationcode' => 'fr/PM',
                    'issuenumber' => '315',
                ],
            ],
            'authors' => (object)[
                'CB' => 'Carl Barks',
                'DR' => 'Don Rosa',
            ],
            'publicationTitles' => (object)[
                'fr/PM' => 'Picsou Magazine'
            ],
            'storyDetails' => (object)[
                'AR 201' => (object)[
                    'storycomment' => 'Comment of story AR 201',
                    'title' => 'Title of story AR 201',
                    'personcode' => 'DR',
                ],
                'W WDC 130-02' => (object)[
                    'storycomment' => 'Comment of story W WDC 130-02',
                    'title' => 'Title of story W WDC 130-02',
                    'personcode' => 'CB',
                ],
            ],
        ], $objectResponse);
    }


    public function testGetSuggestionsForNotifiedCountries(): void {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues/countries_to_notify/_', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals((object)[
            'maxScore' => 6,
            'minScore' => 2,
            'issues' => (object)[
                'fr/PM 315' => (object)[
                    'oldestdate' => (new \DateTime('today'))->format('Y-m-d'),
                    'stories' => (object)[
                        'CB' => ['W WDC 130-02'],
                        'DR' => ['AR 201'],
                    ],
                    'score' => 6,
                    'publicationcode' => 'fr/PM',
                    'issuenumber' => '315',
                ],
                'fr/DDD 1' => (object)[
                    'oldestdate' => (new \DateTime('-5 days'))->format('Y-m-d'),
                    'stories' => (object)[
                        'CB' => ['W WDC  32-02'],
                    ],
                    'score' => 2,
                    'publicationcode' => 'fr/DDD',
                    'issuenumber' => '1',
                ],
            ],
            'authors' => (object)[
                'CB' => 'Carl Barks',
                'DR' => 'Don Rosa',
            ],
            'publicationTitles' => (object)[
                'fr/DDD' => 'Dynastie',
                'fr/PM' => 'Picsou Magazine',
            ],
            'storyDetails' => (object)[
                'AR 201' => (object)[
                    'storycomment' => 'Comment of story AR 201',
                    'title' => 'Title of story AR 201',
                    'personcode' => 'DR',
                ],
                'W WDC  32-02' => (object)[
                    'storycomment' => 'Comment of story W WDC  32-02',
                    'title' => 'Title of story W WDC  32-02',
                    'personcode' => 'CB',
                ],
                'W WDC 130-02' => (object)[
                    'storycomment' => 'Comment of story W WDC 130-02',
                    'title' => 'Title of story W WDC 130-02',
                    'personcode' => 'CB',
                ],
            ],
        ], $objectResponse
        );
    }
}
