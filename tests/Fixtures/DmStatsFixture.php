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
    public static $userId;

    /**
     * @param int $userId
     */
    public function __construct(int $userId = null) {
        self::$userId = $userId;
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

    public function load(ObjectManager $dmStatsEm) : void
    {
        // Author 1
        $dmStatsEm->persist(
            ($authorUser1 = new AuteursPseudos())
                ->setIdUser(self::$userId)
                ->setNomauteurabrege('CB')
                ->setNotation(2)
        );

        $dmStatsEm->persist(
            ($author1Story1 = new AuteursHistoires())
                ->setPersoncode('CB')
                ->setStorycode(self::generateStory('ARC CBL 5B')->getStorycode())
        ); // Missing, 1 issue suggested

        $dmStatsEm->persist(
            ($author1Story2 = new AuteursHistoires())
                ->setPersoncode('CB')
                ->setStorycode(self::generateStory('W WDC  32-02')->getStorycode())
        ); // Missing, 2 issue suggested (the same as story 1 + another one)

        $dmStatsEm->persist(
            ($author1Story3 = new AuteursHistoires())
                ->setPersoncode('CB')
                ->setStorycode(self::generateStory('W WDC  31-05')->getStorycode())
        ); // Not missing for user

        $dmStatsEm->persist(
            ($author1Story4 = new AuteursHistoires())
                ->setPersoncode('CB')
                ->setStorycode(self::generateStory('W WDC 130-02')->getStorycode())
        ); // Missing, 2 issues suggested

        $dmStatsEm->persist(
            ($missingAuthor1Story1ForUser = new UtilisateursHistoiresManquantes())
                ->setPersoncode($author1Story1->getPersoncode())
                ->setStorycode($author1Story1->getStorycode())
                ->setIdUser(self::$userId)
        );

        $dmStatsEm->persist(
            ($missingAuthor1Story2ForUser = new UtilisateursHistoiresManquantes())
                ->setPersoncode($author1Story2->getPersoncode())
                ->setStorycode($author1Story2->getStorycode())
                ->setIdUser(self::$userId)
        );

        $dmStatsEm->persist(
            ($missingAuthor1Story4ForUser = new UtilisateursHistoiresManquantes())
                ->setPersoncode($author1Story4->getPersoncode())
                ->setStorycode($author1Story4->getStorycode())
                ->setIdUser(self::$userId)
        );

        $dmStatsEm->persist(
            ($missingAuthor1Issue1Story1ForUser = new UtilisateursPublicationsManquantes())
                ->setPersoncode($author1Story1->getPersoncode())
                ->setStorycode($author1Story1->getStorycode())
                ->setIdUser(self::$userId)
                ->setPublicationcode(self::generateIssue('us/CBL 7')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('us/CBL 7')->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $dmStatsEm->persist(
            ($missingAuthor1Issue1Story2ForUser = new UtilisateursPublicationsManquantes())
                ->setStorycode($author1Story2->getStorycode())
                ->setIdUser(self::$userId)
                ->setPublicationcode(self::generateIssue('us/CBL 7')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('us/CBL 7')->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $dmStatsEm->persist(
            ($missingAuthor1Issue2Story2ForUser = new UtilisateursPublicationsManquantes())
                ->setPersoncode($author1Story2->getPersoncode())
                ->setStorycode($author1Story2->getStorycode())
                ->setIdUser(self::$userId)
                ->setPublicationcode(self::generateIssue('fr/DDD 1')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/DDD 1')->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $dmStatsEm->persist(
            ($missingAuthor1Issue1Story4ForUser = new UtilisateursPublicationsManquantes())
                ->setStorycode($author1Story4->getStorycode())
                ->setIdUser(self::$userId)
                ->setPublicationcode(self::generateIssue('fr/PM 315')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/PM 315')->getIssuenumber())
                ->setNotation($authorUser1->getNotation())
        );

        $dmStatsEm->flush();

        // Author 2

        $dmStatsEm->persist(
            ($authorUser2 = new AuteursPseudos())
                ->setIdUser(self::$userId)
                ->setNomauteurabrege('DR')
                ->setNotation(4)
        );

        $dmStatsEm->persist(
            ($author2Story5 = new AuteursHistoires())
                ->setPersoncode('DR')
                ->setStorycode(self::generateStory('AR 201')->getStorycode())
        );  // Missing, 1 issue suggested

        $dmStatsEm->persist(
            ($missingAuthor2Story1ForUser = new UtilisateursHistoiresManquantes())
                ->setPersoncode($author2Story5->getPersoncode())
                ->setStorycode($author2Story5->getStorycode())
                ->setIdUser(self::$userId)
        );

        $dmStatsEm->persist(
            ($missingAuthor2Issue5Story5ForUser = new UtilisateursPublicationsManquantes())
                ->setPersoncode($author2Story5->getPersoncode())
                ->setStorycode($author2Story5->getStorycode())
                ->setIdUser(self::$userId)
                ->setPublicationcode(self::generateIssue('fr/PM 315')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/PM 315')->getIssuenumber())
                ->setNotation($authorUser2->getNotation())
        );

        $dmStatsEm->flush();

        // Suggested issues

        $dmStatsEm->persist(
            (new UtilisateursPublicationsSuggerees())
                ->setPublicationcode(self::generateIssue('us/CBL 7')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('us/CBL 7')->getIssuenumber())
                ->setIdUser($authorUser1->getIdUser())
                ->setScore($missingAuthor1Issue1Story2ForUser->getNotation() + $missingAuthor1Issue1Story2ForUser->getNotation())
        );

        $dmStatsEm->persist(
            (new UtilisateursPublicationsSuggerees())
                ->setPublicationcode(self::generateIssue('fr/DDD 1')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/DDD 1')->getIssuenumber())
                ->setIdUser($authorUser1->getIdUser())
                ->setScore($missingAuthor1Issue2Story2ForUser->getNotation())
        );

        $dmStatsEm->persist(
            (new UtilisateursPublicationsSuggerees())
                ->setPublicationcode(self::generateIssue('fr/PM 315')->getPublicationcode())
                ->setIssuenumber(self::generateIssue('fr/PM 315')->getIssuenumber())
                ->setIdUser($authorUser1->getIdUser())
                ->setScore($missingAuthor1Issue1Story4ForUser->getNotation() + $missingAuthor2Issue5Story5ForUser->getNotation())
        );
        $dmStatsEm->flush();
        $dmStatsEm->clear();
    }
}
