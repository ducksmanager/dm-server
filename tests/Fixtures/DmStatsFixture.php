<?php

namespace App\Tests\Fixtures;

use App\Entity\Coa\InducksIssue;
use App\Entity\Coa\InducksStory;
use App\Entity\DmStats\AuteursHistoires;
use App\Entity\DmStats\AuteursPseudos;
use App\Entity\DmStats\UtilisateursHistoiresManquantes;
use App\Entity\DmStats\UtilisateursPublicationsManquantes;
use App\Entity\DmStats\UtilisateursPublicationsSuggerees;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class DmStatsFixture implements FixtureInterface
{
    protected $userId;

    /**
     * @param int $userId
     */
    public function __construct(int $userId = null) {
        $this->userId = $userId;
    }

    private static function generateStory($storyCode): InducksStory
    {
        $story = new InducksStory();
        $story->setStorycode($storyCode);

        return $story;
    }

    private static function generateIssue($issueCode): InducksIssue
    {
        $issueCodeParts = explode(' ', $issueCode);
        $issue = new InducksIssue();
        $issue->setIssuecode($issueCode);
        $issue->setPublicationcode($issueCodeParts[0]);
        $issue->setIssuenumber($issueCodeParts[count($issueCodeParts) - 1]);

        return $issue;
    }

    public function load(ObjectManager $dmStatsEntityManager) : void
    {
        // Author 1
        $authorUser1 = new AuteursPseudos();
        $dmStatsEntityManager->persist(
            $authorUser1
                ->setIdUser($this->userId)
                ->setNomauteurabrege('CB')
                ->setNotation(2)
        );

        $author1Story1 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author1Story1
                ->setPersoncode('CB')
                ->setStorycode(self::generateStory('ARC CBL 5B')->getStorycode())
        ); // Missing, 1 issue suggested

        $author1Story2 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author1Story2
                ->setPersoncode('CB')
                ->setStorycode(self::generateStory('W WDC  32-02')->getStorycode())
        ); // Missing, 2 issue suggested (the same as story 1 + another one)

        $author1Story3 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author1Story3
                ->setPersoncode('CB')
                ->setStorycode(self::generateStory('W WDC  31-05')->getStorycode())
        ); // Not missing for user

        $author1Story4 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author1Story4
                ->setPersoncode('CB')
                ->setStorycode(self::generateStory('W WDC 130-02')->getStorycode())
        ); // Missing, 2 issues suggested

        $missingAuthor1Story1ForUser = new UtilisateursHistoiresManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Story1ForUser
                ->setPersoncode($author1Story1->getPersoncode())
                ->setStorycode($author1Story1->getStorycode())
                ->setIdUser($this->userId)
        );

        $missingAuthor1Story2ForUser = new UtilisateursHistoiresManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Story2ForUser
                ->setPersoncode($author1Story2->getPersoncode())
                ->setStorycode($author1Story2->getStorycode())
                ->setIdUser($this->userId)
        );

        $missingAuthor1Story4ForUser = new UtilisateursHistoiresManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Story4ForUser
                ->setPersoncode($author1Story4->getPersoncode())
                ->setStorycode($author1Story4->getStorycode())
                ->setIdUser($this->userId)
        );

        $missingAuthor1Issue1Story1ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Issue1Story1ForUser
                ->setPersoncode($author1Story1->getPersoncode())
                ->setStorycode($author1Story1->getStorycode())
                ->setIdUser($this->userId)
                ->setPublicationcode(self::generateIssue('us/CBL 7')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('us/CBL 7')->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $missingAuthor1Issue1Story2ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Issue1Story2ForUser->setPersoncode($author1Story2->getPersoncode())
                ->setStorycode($author1Story2->getStorycode())
                ->setIdUser($this->userId)
                ->setPublicationcode(self::generateIssue('us/CBL 7')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('us/CBL 7')->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $missingAuthor1Issue2Story2ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Issue2Story2ForUser->setPersoncode($author1Story2->getPersoncode())
                ->setStorycode($author1Story2->getStorycode())
                ->setIdUser($this->userId)
                ->setPublicationcode(self::generateIssue('fr/DDD 1')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/DDD 1')->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $missingAuthor1Issue1Story4ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor1Issue1Story4ForUser->setPersoncode($author1Story4->getPersoncode())
                ->setStorycode($author1Story4->getStorycode())
                ->setIdUser($this->userId)
                ->setPublicationcode(self::generateIssue('fr/PM 315')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/PM 315')->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $dmStatsEntityManager->flush();

        // Author 2

        $authorUser2 = new AuteursPseudos();
        $dmStatsEntityManager->persist(
            $authorUser2
                ->setIdUser($this->userId)
                ->setNomauteurabrege('DR')
                ->setNotation(4)
        );

        $author2Story5 = new AuteursHistoires();
        $dmStatsEntityManager->persist(
            $author2Story5
                ->setPersoncode('DR')
                ->setStorycode(self::generateStory('AR 201')->getStorycode())
        );  // Missing, 1 issue suggested

        $missingAuthor2Story1ForUser = new UtilisateursHistoiresManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor2Story1ForUser
                ->setPersoncode($author2Story5->getPersoncode())
                ->setStorycode($author2Story5->getStorycode())
                ->setIdUser($this->userId)
        );

        $missingAuthor2Issue5Story5ForUser = new UtilisateursPublicationsManquantes();
        $dmStatsEntityManager->persist(
            $missingAuthor2Issue5Story5ForUser
                ->setPersoncode($author2Story5->getPersoncode())
                ->setStorycode($author2Story5->getStorycode())
                ->setIdUser($this->userId)
                ->setPublicationcode(self::generateIssue('fr/PM 315')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/PM 315')->getIssuenumber())
                ->setNotation($authorUser2->getNotation())
        );

        $dmStatsEntityManager->flush();

        // Suggested issues

        $suggestedIssue1ForUser = new UtilisateursPublicationsSuggerees();
        $dmStatsEntityManager->persist(
            $suggestedIssue1ForUser
                ->setPublicationcode(self::generateIssue('us/CBL 7')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('us/CBL 7')->getIssuenumber())
                ->setIdUser($authorUser1->getIdUser())
                ->setScore($missingAuthor1Issue1Story2ForUser->getNotation() + $missingAuthor1Issue1Story2ForUser->getNotation())
        );

        $suggestedIssue2ForUser = new UtilisateursPublicationsSuggerees();
        $dmStatsEntityManager->persist(
            $suggestedIssue2ForUser
                ->setPublicationcode(self::generateIssue('fr/DDD 1')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/DDD 1')->getIssuenumber())
                ->setIdUser($authorUser1->getIdUser())
                ->setScore($missingAuthor1Issue2Story2ForUser->getNotation())
        );

        $suggestedIssue3ForUser = new UtilisateursPublicationsSuggerees();
        $dmStatsEntityManager->persist(
            $suggestedIssue3ForUser
                ->setPublicationcode(self::generateIssue('fr/PM 315')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/PM 315')->getIssuenumber())
                ->setIdUser($authorUser1->getIdUser())
                ->setScore($missingAuthor1Issue1Story4ForUser->getNotation() + $missingAuthor2Issue5Story5ForUser->getNotation())
        );
        $dmStatsEntityManager->flush();
        $dmStatsEntityManager->clear();
    }
}