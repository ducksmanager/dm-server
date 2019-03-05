<?php

namespace App\Tests\Fixtures;

use App\Entity\Coa\InducksCountryname;
use App\Entity\Coa\InducksEntry;
use App\Entity\Coa\InducksEntryurl;
use App\Entity\Coa\InducksIssue;
use App\Entity\Coa\InducksPerson;
use App\Entity\Coa\InducksPublication;
use App\Entity\Coa\InducksStory;
use App\Entity\Coa\InducksStoryversion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CoaFixture extends Fixture implements FixtureGroupInterface
{

    /** @var InducksCountryname[] $testCountries */
    private static $testCountries = [];

    /** @var InducksPublication[] $testPublications */
    private static $testPublications = [];

    /** @var InducksIssue[] $testIssues */
    private static $testIssues = [];

    /** @var InducksStory[] $testStories */
    private static $testStories = [];

    /** @var InducksStoryversion[] $testStoryversions */
    private static $testStoryversions = [];

    /** @var InducksEntry[] $testEntries */
    private static $testEntries = [];

    /** @var InducksEntryurl[] $testEntryurls */
    private static $testEntryurls = [];

    public function load(ObjectManager $coaEntityManager) : void
    {

        self::$testCountries['frLocale-fr'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['frLocale-fr']
                ->setCountrycode('fr')
                ->setLanguagecode('fr')
                ->setCountryname('France')
        );

        self::$testCountries['frLocale-es'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['frLocale-es']
                ->setCountrycode('es')
                ->setLanguagecode('fr')
                ->setCountryname('Espagne')
        );

        self::$testCountries['frLocale-us'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['frLocale-us']
                ->setCountrycode('us')
                ->setLanguagecode('fr')
                ->setCountryname('USA')
        );

        self::$testCountries['frLocale-fake'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['frLocale-fake']
                ->setCountrycode('fake')
                ->setLanguagecode('fr')
                ->setCountryname('')
        );

        self::$testCountries['esLocale-fr'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['esLocale-fr']
                ->setCountrycode('fr')
                ->setLanguagecode('es')
                ->setCountryname('Francia')
        );

        self::$testCountries['esLocale-es'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['esLocale-es']
                ->setCountrycode('es')
                ->setLanguagecode('es')
                ->setCountryname('España')
        );

        self::$testCountries['esLocale-us'] = new InducksCountryname();
        $coaEntityManager->persist(
            self::$testCountries['esLocale-us']
                ->setCountrycode('us')
                ->setLanguagecode('es')
                ->setCountryname('EE.UU.')
        );

        self::$testPublications['fr/MP'] = new InducksPublication();
        $coaEntityManager->persist(
            self::$testPublications['fr/MP']
                ->setPublicationcode('fr/MP')
                ->setCountrycode('fr')
                ->setTitle('Parade')
        );

        self::$testPublications['fr/DDD'] = new InducksPublication();
        $coaEntityManager->persist(
            self::$testPublications['fr/DDD']
                ->setPublicationcode('fr/DDD')
                ->setCountrycode('fr')
                ->setTitle('Dynastie')
        );

        self::$testPublications['us/CBL'] = new InducksPublication();
        $coaEntityManager->persist(
            self::$testPublications['us/CBL']
                ->setPublicationcode('us/CBL')
                ->setCountrycode('us')
                ->setTitle('Carl Barks Library')
        );

        self::$testIssues['fr/DDD 1'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/DDD 1']
                ->setPublicationcode('fr/DDD')
                ->setIssuenumber('1')
                ->setIssuecode('fr/DDD 1')
        );

        self::$testIssues['fr/DDD 2'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/DDD 2']
                ->setPublicationcode('fr/DDD')
                ->setIssuenumber('2')
                ->setIssuecode('fr/DDD 2')
        );

        self::$testIssues['fr/MP 300'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/MP 300']
                ->setPublicationcode('fr/MP')
                ->setIssuenumber('300')
                ->setIssuecode('fr/MP 300')
        );

        self::$testIssues['fr/PM 315'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/PM 315']
                ->setPublicationcode('fr/PM')
                ->setIssuenumber('315')
                ->setIssuecode('fr/PM 315')
        );

        self::$testIssues['us/CBL 7'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['us/CBL 7']
                ->setPublicationcode('us/CBL')
                ->setIssuenumber('7')
                ->setIssuecode('us/CBL 7')
        );

        self::$testIssues['de/MM1951-00'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['de/MM1951-00']
                ->setPublicationcode('de/MM')
                ->setIssuenumber('1951-00')
                ->setIssuecode('de/MM1951-00')
        );

        self::$testIssues['fr/CB PN  1'] = new InducksIssue();
        $coaEntityManager->persist(
            self::$testIssues['fr/CB PN  1']
                ->setPublicationcode('fr/CB')
                ->setIssuenumber('PN  1')
                ->setIssuecode('fr/CB PN  1')
        );

        self::$testStories['W WDC  31-05'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['W WDC  31-05']
                ->setTitle('Title of story W WDC  31-05')
                ->setStorycomment('Comment of story W WDC  31-05')
                ->setStorycode('W WDC  31-05')
        );

        self::$testStories['W WDC  32-02'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['W WDC  32-02']
                ->setTitle('Title of story W WDC  32-02')
                ->setStorycomment('Comment of story W WDC  32-02')
                ->setStorycode('W WDC  32-02')
        );

        self::$testStories['ARC CBL 5B'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['ARC CBL 5B']
                ->setTitle('Title of story ARC CBL 5B')
                ->setStorycomment('Comment of story ARC CBL 5B')
                ->setStorycode('ARC CBL 5B')
        );

        self::$testStories['W WDC 130-02'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['W WDC 130-02']
                ->setTitle('Title of story W WDC 130-02')
                ->setStorycomment('Comment of story W WDC 130-02')
                ->setStorycode('W WDC 130-02')
        );

        self::$testStories['AR 201'] = new InducksStory();
        $coaEntityManager->persist(
            self::$testStories['AR 201']
                ->setTitle('Title of story AR 201')
                ->setStorycomment('Comment of story AR 201')
                ->setStorycode('AR 201')
        );

        self::$testStoryversions['W WDC  31-05'] = new InducksStoryversion();
        $coaEntityManager->persist(
            self::$testStoryversions['W WDC  31-05']
                ->setStoryversioncode('W WDC  31-05')
                ->setStorycode('W WDC  31-05')
        );

        self::$testStoryversions['de/SPBL 136c'] = new InducksStoryversion();
        $coaEntityManager->persist(
            self::$testStoryversions['de/SPBL 136c']
                ->setStoryversioncode('de/SPBL 136c')
                ->setStorycode('W WDC  31-05')
        );

        self::$testEntries['us/CBL 7a'] = new InducksEntry();
        $coaEntityManager->persist(
            self::$testEntries['us/CBL 7a']
                ->setEntrycode('us/CBL 7a')
                ->setIssuecode('fr/DDD 1')
                ->setStoryversioncode('W WDC  31-05')
        );

        self::$testEntryurls['us/CBL 7p000a'] = new InducksEntryurl();
        $coaEntityManager->persist(
            self::$testEntryurls['us/CBL 7p000a']
                ->setEntrycode('us/CBL 7p000a')
                ->setUrl('us/cbl/us_cbl_7p000a_001.png')
                ->setSitecode('thumbnails')
        );

        $inducksPerson = new InducksPerson();
        $coaEntityManager->persist(
            $inducksPerson
                ->setPersoncode('CB')
                ->setFullname('Carl Barks')
        );

        $inducksPerson = new InducksPerson();
        $coaEntityManager->persist(
            $inducksPerson->setPersoncode('DR')
                ->setFullname('Don Rosa')
        );
        $coaEntityManager->flush();
        $coaEntityManager->clear();
    }

    /**
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['coa'];
    }
}