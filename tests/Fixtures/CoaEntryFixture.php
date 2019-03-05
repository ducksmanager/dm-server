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
    private $storyCode;
    private $entryUrl;
    private $publicationCode;
    private $issueNumber;

    public function __construct(string $storyCode = '', string $entryUrl = '', string $publicationCode = '', string $issueNumber = '') {
        $this->storyCode = $storyCode;
        $this->entryUrl = $entryUrl;
        $this->publicationCode = $publicationCode;
        $this->issueNumber = $issueNumber;
    }

    public function load(ObjectManager $coaEntityManager) : void
    {
        $originalEntryCode = $this->storyCode.'-entry-1';
        $originEntry = new InducksEntry();
        $coaEntityManager->persist(
            $originEntry
                ->setEntrycode($originalEntryCode)
                ->setStoryversioncode($this->storyCode.'-1')
        );

        $originEntryurl = new InducksEntryurl();
        $coaEntityManager->persist(
            $originEntryurl
                ->setEntrycode($originalEntryCode)
                ->setUrl($this->entryUrl)
        );

        $originStoryversion = new InducksStoryversion();
        $coaEntityManager->persist(
            $originStoryversion
                ->setStorycode($this->storyCode)
                ->setStoryversioncode($this->storyCode.'-1')
        );

        // Create similar entry / entryurl / storyversion

        $relatedEntryCode = $this->storyCode.'-entry-2';

        $relatedStoryversion = new InducksStoryversion();
        $coaEntityManager->persist(
            $relatedStoryversion
                ->setStorycode($this->storyCode)
                ->setStoryversioncode($this->storyCode.'-2')
        );

        $relatedEntry = new InducksEntry();
        $coaEntityManager->persist(
            $relatedEntry
                ->setEntrycode($relatedEntryCode)
                ->setIssuecode($this->publicationCode.' '.$this->issueNumber)
                ->setStoryversioncode($this->storyCode.'-2')
        );

        $relatedIssue = new InducksIssue();
        $coaEntityManager->persist(
            $relatedIssue
                ->setIssuecode($this->publicationCode.' '.$this->issueNumber)
                ->setPublicationcode($this->publicationCode)
                ->setIssuenumber($this->issueNumber)
        );

        $relatedEntryUrl = new InducksEntryurl();
        $coaEntityManager->persist(
            $relatedEntryUrl
                ->setEntrycode($relatedEntryCode)
                ->setUrl($this->entryUrl.'-2')
        );

        $coaEntityManager->flush();
    }
}