<?php

namespace App\Tests\Fixtures;

use App\Entity\Coverid\Covers;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CoverIdFixture implements FixtureInterface
{
    public static array $urls;

    public function load(ObjectManager $dmEm) : void
    {
        foreach(self::$urls as $issueCode => $url) {
            $dmEm->persist(
                (new Covers())
                    ->setSitecode('webusers')
                    ->setIssuecode($issueCode)
                    ->setUrl($url)
            );
        }

        $dmEm->flush();
    }
}
