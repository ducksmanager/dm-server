<?php

namespace App\Tests\Fixtures;

use App\Entity\Dm\TranchesDoublons;
use App\Entity\Dm\TranchesPretes;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EdgesFixture implements FixtureInterface
{
    public function load(ObjectManager $dmEm) : void
    {
        $dmEm->persist(
            ($edge1 = new TranchesPretes())
                ->setPublicationcode('fr/JM')
                ->setIssuenumber('3001')
                ->setDateajout(new DateTime())
                ->setSlug('edges-fr-JM-3001')
        );

        $dmEm->persist(
            (new TranchesDoublons())
                ->setPays('fr')
                ->setMagazine('JM')
                ->setNumero('3002')
                ->setNumeroreference($edge1->getIssuenumber())
                ->setTranchereference($edge1)
        );

        $dmEm->persist(
            ($edge2 = new TranchesPretes())
                ->setPublicationcode('fr/JM')
                ->setIssuenumber('4001')
                ->setDateajout(new DateTime())
                ->setSlug('edges-fr-JM-4001')
        );

        $dmEm->persist(
            (new TranchesDoublons())
                ->setPays('fr')
                ->setMagazine('JM')
                ->setNumero('4002')
                ->setNumeroreference($edge2->getIssuenumber())
                ->setTranchereference($edge2)
        );

        $dmEm->flush();
    }
}
