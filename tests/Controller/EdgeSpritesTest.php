<?php
namespace App\Tests;

use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\TranchesPretesSprites;
use App\Helper\SpriteHelper;
use App\Tests\Fixtures\EdgesFixture;
use DateTime;

class EdgeSpritesTest extends TestCommon
{
    protected function getEmNamesToCreate() : array {
        return ['dm'];
    }

    public function testUploadEdgeAndGenerateSprite() {
        $this->loadFixtures([ EdgesFixture::class ], true, 'dm');
        $edge = $this->getEm('dm')->getRepository(TranchesPretes::class)->findOneBy([
            'publicationcode' => 'fr/JM',
            'issuenumber' => '3001'
        ]);

        SpriteHelper::$mockedResults = [
            'upload' => 'Success',
            'add_tag' => 'Success',
            'generate_sprite' => [
                'edges-fr-JM-3001-3010' => ['version' => 1],
                'edges-fr-JM-3001-3020' => ['version' => 2],
                'edges-fr-JM-3001-3050' => ['version' => 3],
                'edges-fr-JM-3001-3100' => ['version' => 4],
                'edges-fr-JM-full' => ['version' => 5]
            ]
        ];

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgesprites/{$edge->getId()}", self::$adminUser, 'PUT')->call();
        $objectResponse = json_decode($this->getResponseContent($response), false);
        $this->assertEquals((object) [
            'edgesToUpload' => [
                (object) [
                    'publicationcode' => 'fr/JM',
                    'issuenumber' => '3001',
                    'slug' => 'edges-fr-JM-3001',
                ],
            ],
            'spriteNames' => (object)[
                'edges-fr-JM-3001-3010' => (object) ['size' => 1],
                'edges-fr-JM-3001-3020' => (object) ['size' => 1],
                'edges-fr-JM-3001-3050' => (object) ['size' => 1],
                'edges-fr-JM-3001-3100' => (object) ['size' => 1],
                'edges-fr-JM-full' => (object) ['size' => 2],
            ],
            'createdSprites' => [
                'edges-fr-JM-3001-3010',
                'edges-fr-JM-3001-3020',
                'edges-fr-JM-3001-3050',
                'edges-fr-JM-3001-3100',
                'edges-fr-JM-full',
            ],
        ], $objectResponse);
    }

    public function testUploadEdgeAndGenerateSpriteNoFullSprite() {
        $this->loadFixtures([ EdgesFixture::class ], true, 'dm');

        for ($i=2000; $i<2100; $i++) {
            $edge = new TranchesPretes();
            $this->getEm('dm')->persist($edge
                ->setPublicationcode('fr/JM')
                ->setIssuenumber($i)
                ->setDateajout(new DateTime())
            );

            $edgeSprite = new TranchesPretesSprites();
            $this->getEm('dm')->persist($edgeSprite
                ->setIdTranche($edge)
                ->setSpriteSize(150)
                ->setSpriteName('edges-fr-JM-full')
            );
        }

        $this->getEm('dm')->flush();

        $edge = $this->getEm('dm')->getRepository(TranchesPretes::class)->findOneBy([
            'publicationcode' => 'fr/JM',
            'issuenumber' => '3001'
        ]);

        SpriteHelper::$mockedResults = [
            'upload' => 'Success',
            'add_tag' => 'Success',
            'generate_sprite' => [
                'edges-fr-JM-3001-3010' => ['version' => 1],
                'edges-fr-JM-3001-3020' => ['version' => 2],
                'edges-fr-JM-3001-3050' => ['version' => 3],
                'edges-fr-JM-3001-3100' => ['version' => 4]
            ]
        ];

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgesprites/{$edge->getId()}", self::$adminUser, 'PUT')->call();
        $objectResponse = json_decode($this->getResponseContent($response), false);
        $this->assertEquals((object) [
            'edgesToUpload' => [
                (object) [
                    'publicationcode' => 'fr/JM',
                    'issuenumber' => '3001',
                    'slug' => 'edges-fr-JM-3001',
                ],
            ],
            'spriteNames' => (object)[
                'edges-fr-JM-3001-3010' => (object) ['size' => 1],
                'edges-fr-JM-3001-3020' => (object) ['size' => 1],
                'edges-fr-JM-3001-3050' => (object) ['size' => 1],
                'edges-fr-JM-3001-3100' => (object) ['size' => 1]
            ],
            'createdSprites' => [
                'edges-fr-JM-3001-3010',
                'edges-fr-JM-3001-3020',
                'edges-fr-JM-3001-3050',
                'edges-fr-JM-3001-3100',
            ],
        ], $objectResponse);
    }
}
