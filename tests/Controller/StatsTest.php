<?php
namespace App\Tests\Controller;

use App\Tests\Fixtures\CoaEntryFixture;
use App\Tests\Fixtures\CoaFixture;
use App\Tests\Fixtures\DmStatsFixture;
use App\Tests\TestCommon;
use DateTime;

class StatsTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm', 'coa', 'dm_stats'];
    }

    public function setUp() : void
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
                    'issuecode' => 'fr/PM 315',
                    'oldestdate' => (new DateTime())->format('Y-m-d')
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
                    'issuecode' => 'us/CBL 7',
                    'oldestdate' => (new DateTime('-5 days midnight'))->format('Y-m-d')
                ],
                'fr/DDD 1' => (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC  32-02'],
                    ],
                    'score' => 2,
                    'publicationcode' => 'fr/DDD',
                    'issuenumber' => '1',
                    'issuecode' => 'fr/DDD 1',
                    'oldestdate' => (new DateTime('-5 days midnight'))->format('Y-m-d')
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
        $this->assertIsObject($objectResponse);
    }

    public function testGetSuggestionsWithLimit(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues/ALL/_/1', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals((object)[
            'maxScore' => 6,
            'minScore' => 6,
            'issues' => (object)[
                (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC 130-02'],
                        'DR' => ['AR 201'],
                    ],
                    'score' => 6,
                    'publicationcode' => 'fr/PM',
                    'issuenumber' => '315',
                    'issuecode' => 'fr/PM 315',
                    'oldestdate' => (new DateTime())->format('Y-m-d')
                ]
            ],
            'authors' => (object)[
                'CB' => 'Carl Barks',
                'DR' => 'Don Rosa',
            ],
            'publicationTitles' => (object)[
                'fr/PM' => 'Picsou Magazine',
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
        $this->assertIsObject($objectResponse);
    }

    public function testGetSuggestionsByCountry(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues/fr', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals((object)[
            'maxScore' => 6,
            'minScore' => 2,
            'issues' => (object)[
                (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC 130-02'],
                        'DR' => ['AR 201'],
                    ],
                    'score' => 6,
                    'publicationcode' => 'fr/PM',
                    'issuenumber' => '315',
                    'issuecode' => 'fr/PM 315',
                    'oldestdate' => (new DateTime())->format('Y-m-d')
                ],
                (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC  32-02'],
                    ],
                    'score' => 2,
                    'publicationcode' => 'fr/DDD',
                    'issuenumber' => '1',
                    'issuecode' => 'fr/DDD 1',
                    'oldestdate' => (new DateTime('-5 days midnight'))->format('Y-m-d')
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
    }

    public function testGetSuggestionsSincePreviousVisit(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/stats/suggestedissues/fr/since_previous_visit', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals((object)[
            'maxScore' => 6,
            'minScore' => 6,
            'issues' => (object)[
                (object)[
                    'stories' => (object)[
                        'CB' => ['W WDC 130-02'],
                        'DR' => ['AR 201'],
                    ],
                    'score' => 6,
                    'publicationcode' => 'fr/PM',
                    'issuenumber' => '315',
                    'issuecode' => 'fr/PM 315',
                    'oldestdate' => (new DateTime())->format('Y-m-d')
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
                (object)[
                    'oldestdate' => (new DateTime('today'))->format('Y-m-d'),
                    'stories' => (object)[
                        'CB' => ['W WDC 130-02'],
                        'DR' => ['AR 201'],
                    ],
                    'score' => 6,
                    'publicationcode' => 'fr/PM',
                    'issuenumber' => '315',
                    'issuecode' => 'fr/PM 315',
                ],
                (object)[
                    'oldestdate' => (new DateTime('-5 days'))->format('Y-m-d'),
                    'stories' => (object)[
                        'CB' => ['W WDC  32-02'],
                    ],
                    'score' => 2,
                    'publicationcode' => 'fr/DDD',
                    'issuenumber' => '1',
                    'issuecode' => 'fr/DDD 1',
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
