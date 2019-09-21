<?php

namespace App\Tests\Fixtures;

use App\Entity\Coa\InducksEntry;
use App\Entity\Coa\InducksEntryurl;
use App\Entity\Coa\InducksIssue;
use App\Entity\Coa\InducksStoryversion;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CoaEntryFixture implements FixtureInterface
{
    public static $storyCode;
    public static $entryUrl;
    public static $publicationCode;
    public static $issueNumber;

    public function load(ObjectManager $coaEntityManager) : void
    {
        $originalEntryCode = self::$storyCode.'-entry-1';
        $originEntry = new InducksEntry();
        $coaEntityManager->persist(
            $originEntry
                ->setEntrycode($originalEntryCode)
                ->setStoryversioncode(self::$storyCode.'-1')
        );

        $coaEntityManager->persist(
            $originEntryurl = (new InducksEntryurl())
                ->setEntrycode($originalEntryCode)
                ->setUrl(self::$entryUrl)
        );

        $coaEntityManager->persist(
            $originStoryversion = (new InducksStoryversion())
                ->setStorycode(self::$storyCode)
                ->setStoryversioncode(self::$storyCode.'-1')
        );

        // Create similar entry / entryurl / storyversion

        $relatedEntryCode = self::$storyCode.'-entry-2';

        $coaEntityManager->persist(
            $relatedStoryversion = (new InducksStoryversion())
                ->setStorycode(self::$storyCode)
                ->setStoryversioncode(self::$storyCode.'-2')
        );

        $coaEntityManager->persist(
            (new InducksEntry())
                ->setEntrycode($relatedEntryCode)
                ->setIssuecode(self::$publicationCode.' '.self::$issueNumber)
                ->setStoryversioncode(self::$storyCode.'-2')
        );

        $coaEntityManager->persist(
            (new InducksIssue())
                ->setIssuecode(self::$publicationCode.' '.self::$issueNumber)
                ->setPublicationcode(self::$publicationCode)
                ->setIssuenumber(self::$issueNumber)
        );

        $coaEntityManager->persist(
            (new InducksEntryurl())
                ->setEntrycode($relatedEntryCode)
                ->setUrl(self::$entryUrl.'-2')
        );

        $coaEntityManager->flush();
    }
}
