<?php

namespace App\Tests\Fixtures;

use App\Entity\Coverid\Covers;
use App\Tests\TestCommon;
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

//        @mkdir($_ENV['IMAGE_REMOTE_ROOT'].dirname($this->url), 0777, true);
//        $imagePath = self::getPathToFileToUpload(TestCommon::$exampleImage);
//        copy($imagePath, $_ENV['IMAGE_REMOTE_ROOT'] . $this->url);

        $dmEntityManager->flush();
    }

    private static function getPathToFileToUpload($fileName) {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, $fileName]);
    }
}
