<?php

namespace App\Tests\Fixtures;

use App\Entity\Dm\TranchesDoublons;
use App\Entity\Dm\TranchesPretes;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EdgesFixture implements FixtureInterface
{

    public function load(ObjectManager $dmEntityManager) : void
    {
        $edge1 = new TranchesPretes();
        $dmEntityManager->persist(
            $edge1
                ->setPublicationcode('fr/JM')
                ->setIssuenumber('3001')
                ->setDateajout(new \DateTime())
        );

        $dupEdge1 = new TranchesDoublons();
        $dmEntityManager->persist(
            $dupEdge1
                ->setPays('fr')
                ->setMagazine('JM')
                ->setNumero('3002')
                ->setNumeroreference($edge1->getIssuenumber())
                ->setTranchereference($edge1)
        );

        $edge2 = new TranchesPretes();
        $dmEntityManager->persist(
            $edge2
                ->setPublicationcode('fr/JM')
                ->setIssuenumber('4001')
                ->setDateajout(new \DateTime())
        );

        $dupEdge2 = new TranchesDoublons();
        $dmEntityManager->persist(
            $dupEdge2
                ->setPays('fr')
                ->setMagazine('JM')
                ->setNumero('4002')
                ->setNumeroreference($edge2->getIssuenumber())
                ->setTranchereference($edge2)
        );

        $dmEntityManager->flush();
    }
}