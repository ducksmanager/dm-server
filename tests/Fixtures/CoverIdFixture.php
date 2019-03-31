<?php

namespace App\Tests\Fixtures;

use App\Entity\Coverid\Covers;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CoverIdFixture implements FixtureInterface
{
    private $issueCode;
    private $url;

    public function __construct(string $issueCode = '', string $url = '')
    {
        $this->issueCode = $issueCode;
        $this->url = $url;
    }

    public function load(ObjectManager $dmEntityManager) : void
    {
        $cover = new Covers();
        $dmEntityManager->persist(
            $cover
                ->setSitecode('webusers')
                ->setIssuecode($this->issueCode)
                ->setUrl($this->url)
        );

        $dmEntityManager->flush();
    }
}
