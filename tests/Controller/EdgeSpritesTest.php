<?php
namespace App\Tests;

use App\Entity\Dm\TranchesPretes;
use App\Helper\SpriteHelper;
use App\Tests\Fixtures\EdgesFixture;

class EdgeSpritesTest extends TestCommon
{
    protected function getEmNamesToCreate() : array {
        return ['dm'];
    }

    public function testUploadEdgeAndGenerateSprite() {
        $this->loadFixture('dm', new EdgesFixture());
        $edge = $this->getEm('dm')->getRepository(TranchesPretes::class)->findOneBy([
            'publicationcode' => 'fr/JM',
            'issuenumber' => '3001'
        ]);

        SpriteHelper::$mockedResults = [
            'upload' => 'Success',
            'add_tag' => 'Success',
            'generate_sprite' => [
                'edges-fr-JM-3001-3010' => ['version' => 123456789],
                'edges-fr-JM-3001-3020' => ['version' => 123456789],
                'edges-fr-JM-3001-3050' => ['version' => 123456789],
                'edges-fr-JM-3001-3100' => ['version' => 123456789],
                'edges-fr-JM-full' => ['version' => 123456789]
            ]
        ];

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgesprites/from/{$edge->getId()}", self::$adminUser, 'PUT')->call();
        $objectResponse = json_decode($this->getResponseContent($response), false);
        $this->assertEquals((object) [
            'edgesToUpload' => [
                (object) [
                    'publicationcode' => 'fr/JM',
                    'issuenumber' => '3001',
                    'slug' => 'edges-fr-JM-3001',
                ],
                (object) [
                    'publicationcode' => 'fr/JM',
                    'issuenumber' => '4001',
                    'slug' => 'edges-fr-JM-4001',
                ],
            ],
            'spriteNames' => [
                'edges-fr-JM-3001-3010',
                'edges-fr-JM-3001-3020',
                'edges-fr-JM-3001-3050',
                'edges-fr-JM-3001-3100',
                'edges-fr-JM-full',
            ],
            'createdSprites' => [
                (object) ['spriteName' => 'edges-fr-JM-3001-3010'],
                (object) ['spriteName' => 'edges-fr-JM-3001-3020'],
                (object) ['spriteName' => 'edges-fr-JM-3001-3050'],
                (object) ['spriteName' => 'edges-fr-JM-3001-3100'],
            ],
        ], $objectResponse);
    }
}
