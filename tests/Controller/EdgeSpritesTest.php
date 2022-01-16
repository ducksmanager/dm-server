<?php
namespace App\Tests\Controller;

use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\TranchesPretesSprites;
use App\Service\SpriteService;
use App\Tests\Fixtures\EdgesFixture;
use App\Tests\TestCommon;
use Cloudinary\Api\ApiResponse;
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

        SpriteService::$mockedResults = [
            'upload' => new ApiResponse('Success', []),
            'add_tag' => new ApiResponse('Success', []),
            'generate_sprite' => new ApiResponse([
                'edges-fr-JM-3001-3010' => ['version' => 1],
                'edges-fr-JM-3001-3020' => ['version' => 2],
                'edges-fr-JM-3001-3050' => ['version' => 3],
                'edges-fr-JM-3001-3100' => ['version' => 4],
                'edges-fr-JM-full' => ['version' => 5]
            ], [])
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

        SpriteService::$mockedResults = [
            'upload' => new ApiResponse('Success', []),
            'add_tag' => new ApiResponse('Success', []),
            'generate_sprite' => new ApiResponse([
                'edges-fr-JM-3001-3010' => ['version' => 1],
                'edges-fr-JM-3001-3020' => ['version' => 2],
                'edges-fr-JM-3001-3050' => ['version' => 3],
                'edges-fr-JM-3001-3100' => ['version' => 4]
            ], [])
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
