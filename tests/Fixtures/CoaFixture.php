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

        $coaEntityManager->persist(
            (self::$testCountries['frLocale-fr'] = new InducksCountryname())
                ->setCountrycode('fr')
                ->setLanguagecode('fr')
                ->setCountryname('France')
        );

        $coaEntityManager->persist(
            (self::$testCountries['frLocale-es'] = new InducksCountryname())
                ->setCountrycode('es')
                ->setLanguagecode('fr')
                ->setCountryname('Espagne')
        );

        $coaEntityManager->persist(
            (self::$testCountries['frLocale-us'] = new InducksCountryname())
                ->setCountrycode('us')
                ->setLanguagecode('fr')
                ->setCountryname('USA')
        );

        $coaEntityManager->persist(
            (self::$testCountries['frLocale-fake'] = new InducksCountryname())
                ->setCountrycode('fake')
                ->setLanguagecode('fr')
                ->setCountryname('')
        );

        $coaEntityManager->persist(
            (self::$testCountries['esLocale-fr'] = new InducksCountryname())
                ->setCountrycode('fr')
                ->setLanguagecode('es')
                ->setCountryname('Francia')
        );

        $coaEntityManager->persist(
            (self::$testCountries['esLocale-es'] = new InducksCountryname())
                ->setCountrycode('es')
                ->setLanguagecode('es')
                ->setCountryname('España')
        );

        $coaEntityManager->persist(
            (self::$testCountries['esLocale-us'] = new InducksCountryname())
                ->setCountrycode('us')
                ->setLanguagecode('es')
                ->setCountryname('EE.UU.')
        );

        $coaEntityManager->persist(
            (self::$testPublications['fr/MP'] = new InducksPublication())
                ->setPublicationcode('fr/MP')
                ->setCountrycode('fr')
                ->setTitle('Parade')
        );

        $coaEntityManager->persist(
            (self::$testPublications['fr/DDD'] = new InducksPublication())
                ->setPublicationcode('fr/DDD')
                ->setCountrycode('fr')
                ->setTitle('Dynastie')
        );

        $coaEntityManager->persist(
            (self::$testPublications['us/CBL'] = new InducksPublication())
                ->setPublicationcode('us/CBL')
                ->setCountrycode('us')
                ->setTitle('Carl Barks Library')
        );

        $coaEntityManager->persist(
            (self::$testIssues['fr/DDD 1'] = new InducksIssue())
                ->setPublicationcode('fr/DDD')
                ->setIssuenumber('1')
                ->setIssuecode('fr/DDD 1')
        );

        $coaEntityManager->persist(
            (self::$testIssues['fr/DDD 2'] = new InducksIssue())
                ->setPublicationcode('fr/DDD')
                ->setIssuenumber('2')
                ->setIssuecode('fr/DDD 2')
        );

        $coaEntityManager->persist(
            (self::$testIssues['fr/MP 300'] = new InducksIssue())
                ->setPublicationcode('fr/MP')
                ->setIssuenumber('300')
                ->setIssuecode('fr/MP 300')
        );

        $coaEntityManager->persist(
            (self::$testIssues['fr/PM 315'] = new InducksIssue())
                ->setPublicationcode('fr/PM')
                ->setIssuenumber('315')
                ->setIssuecode('fr/PM 315')
        );

        $coaEntityManager->persist(
            (self::$testIssues['us/CBL 7'] = new InducksIssue())
                ->setPublicationcode('us/CBL')
                ->setIssuenumber('7')
                ->setIssuecode('us/CBL 7')
        );

        $coaEntityManager->persist(
            (self::$testIssues['de/MM1951-00'] = new InducksIssue())
                ->setPublicationcode('de/MM')
                ->setIssuenumber('1951-00')
                ->setIssuecode('de/MM1951-00')
        );

        $coaEntityManager->persist(
            (self::$testIssues['fr/CB PN  1'] = new InducksIssue())
                ->setPublicationcode('fr/CB')
                ->setIssuenumber('PN  1')
                ->setIssuecode('fr/CB PN  1')
        );

        $coaEntityManager->persist(
            (self::$testStories['W WDC  31-05'] = new InducksStory())
                ->setTitle('Title of story W WDC  31-05')
                ->setStorycomment('Comment of story W WDC  31-05')
                ->setStorycode('W WDC  31-05')
        );

        $coaEntityManager->persist(
            (self::$testStories['W WDC  32-02'] = new InducksStory())
                ->setTitle('Title of story W WDC  32-02')
                ->setStorycomment('Comment of story W WDC  32-02')
                ->setStorycode('W WDC  32-02')
        );

        $coaEntityManager->persist(
            (self::$testStories['ARC CBL 5B'] = new InducksStory())
                ->setTitle('Title of story ARC CBL 5B')
                ->setStorycomment('Comment of story ARC CBL 5B')
                ->setStorycode('ARC CBL 5B')
        );

        $coaEntityManager->persist(
            (self::$testStories['W WDC 130-02'] = new InducksStory())
                ->setTitle('Title of story W WDC 130-02')
                ->setStorycomment('Comment of story W WDC 130-02')
                ->setStorycode('W WDC 130-02')
        );

        $coaEntityManager->persist(
            (self::$testStories['AR 201'] = new InducksStory())
                ->setTitle('Title of story AR 201')
                ->setStorycomment('Comment of story AR 201')
                ->setStorycode('AR 201')
        );

        $coaEntityManager->persist(
            (self::$testStoryversions['W WDC  31-05'] = new InducksStoryversion())
                ->setStoryversioncode('W WDC  31-05')
                ->setStorycode('W WDC  31-05')
        );

        $coaEntityManager->persist(
            (self::$testStoryversions['de/SPBL 136c'] = new InducksStoryversion())
                ->setStoryversioncode('de/SPBL 136c')
                ->setStorycode('W WDC  31-05')
        );

        $coaEntityManager->persist(
            (self::$testEntries['us/CBL 7a'] = new InducksEntry())
                ->setEntrycode('us/CBL 7a')
                ->setIssuecode('fr/DDD 1')
                ->setStoryversioncode('W WDC  31-05')
        );

        $coaEntityManager->persist(
            (self::$testEntryurls['us/CBL 7p000a'] = new InducksEntryurl())
                ->setEntrycode('us/CBL 7p000a')
                ->setUrl('us/cbl/us_cbl_7p000a_001.png')
                ->setSitecode('thumbnails')
        );

        $coaEntityManager->persist(
            ($inducksPerson = new InducksPerson())
                ->setPersoncode('CB')
                ->setFullname('Carl Barks')
        );

        $coaEntityManager->persist(
            ($inducksPerson = new InducksPerson())
                ->setPersoncode('DR')
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
