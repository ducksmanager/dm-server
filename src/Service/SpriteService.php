<?php

namespace App\Service;

use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\TranchesPretesSprites;
use App\Entity\Dm\TranchesPretesSpritesUrls;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Psr\Log\LoggerInterface;


class SpriteService
{
    private static array $SPRITE_SIZES = [10, 20, 50, 100, 'full'];
    private static int $MAX_SPRITE_SIZE = 100;

    public static ?array $mockedResults = null;
    private LoggerInterface $logger;
    private ObjectManager $dmEm;

    public function __construct(ManagerRegistry $doctrineManagerRegistry, LoggerInterface $logger)
    {
        $this->dmEm = $doctrineManagerRegistry->getManager('dm');
        $this->logger = $logger;
    }

    /**
     * @param TranchesPretes[] $edges
     * @return void
     */
    public function updateTags(array $edges): array
    {
        $qbDeleteExistingSpriteNames = $this->dmEm->createQueryBuilder();
        $qbDeleteExistingSpriteNames
            ->delete(TranchesPretesSprites::class, 'sprite')
            ->andWhere($qbDeleteExistingSpriteNames->expr()->in('sprite.idTranche', array_map(fn(TranchesPretes $edge) => $edge->getId(), $edges)));
        $qbDeleteExistingSpriteNames->getQuery()->execute();

        $tagsToAdd = [];
        $spriteNames = [];

        foreach($edges as $edge) {
            foreach (self::$SPRITE_SIZES as $spriteSize) {
                $spriteName = self::getSpriteName($edge->getPublicationcode(), $spriteSize === 'full'
                    ? 'full'
                    : self::getSpriteRange($edge->getIssuenumber(), $spriteSize)
                );

                if ($spriteSize === 'full') {
                    $spriteSize = $this->dmEm->getRepository(TranchesPretes::class)->count([
                        'publicationcode' => $edge->getPublicationcode()
                    ]);
                    if ($spriteSize > self::$MAX_SPRITE_SIZE) {
                        $this->logger->info("Not creating a full sprite for publication {$edge->getPublicationcode()} : sprite size is too big ($spriteSize)");
                        continue;
                    }
                } else {
                    $spriteSize = $this->dmEm->getRepository(TranchesPretesSprites::class)->count([
                        'spriteName' => $spriteName
                    ]) + 1;
                }

                $this->logger->info("Adding tag $spriteName on {$edge->getSlug()}");
                if (!array_key_exists($spriteName, $tagsToAdd)) {
                    $tagsToAdd[$spriteName] = ['slugs' => [], 'spriteSize' => $spriteSize];
                }
                $tagsToAdd[$spriteName]['slugs'][] = $edge->getSlug();

                $spriteForEdge = new TranchesPretesSprites();
                $this->dmEm->persist($spriteForEdge
                    ->setIdTranche($edge)
                    ->setSpriteName($spriteName)
                    ->setSpriteSize($spriteSize)
                );
            }
        }

        $this->dmEm->flush();

        foreach($tagsToAdd as $spriteName => $slugsAndSpriteSize) {
            $this->addTag($spriteName, $slugsAndSpriteSize['slugs']);

            $qbUpdateOtherEdgesInSprite = $this->dmEm->createQueryBuilder()
                ->update(TranchesPretesSprites::class, 'otherEdgeInSprite')
                ->set('otherEdgeInSprite.spriteSize', $slugsAndSpriteSize['spriteSize'])
                ->andWhere($qbDeleteExistingSpriteNames->expr()->eq('otherEdgeInSprite.spriteName', $qbDeleteExistingSpriteNames->expr()->literal($spriteName)));
            $qbUpdateOtherEdgesInSprite->getQuery()->execute();
        }
    }

    public function generateSprites(): array
    {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('Sprite_name', 'spriteName');
        $spritesWithNoUrl = $this->dmEm->createNativeQuery("
            select distinct Sprite_name
            from tranches_pretes_sprites
            where Sprite_name not in (select sprite_name from tranches_pretes_sprites_urls)
              and Sprite_size < 100", $rsm)->getArrayResult();

        $responsePromises = [];
        foreach ($spritesWithNoUrl as $sprite) {
            ['spriteName' => $spriteName] = $sprite;
            $this->logger->info("Generating sprite for $spriteName...");
            $responsePromises[] = $this->generateSprite($spriteName);
        }

        Utils::all($responsePromises)->then(function (array $responses) use ($spritesWithNoUrl) {
            foreach($responses as $idx => $response) {
                $this->dmEm->persist(
                    (new TranchesPretesSpritesUrls())
                        ->setVersion($response['version'])
                        ->setSpriteName($spritesWithNoUrl[$idx]['spriteName'])
                );
            }
            $this->dmEm->flush();
        })->wait();

        return array_map(fn($spriteWithNoUrl) => $spriteWithNoUrl['spriteName'], $spritesWithNoUrl);
    }

    private static function getSpriteRange(string $issueNumber, int $rangeWidth): string
    {
        $issueNumberInt = (int)$issueNumber;
        return implode('-', [
            $issueNumberInt - ($issueNumberInt - 1) % $rangeWidth,
            ($issueNumberInt - ($issueNumberInt - 1) % $rangeWidth) + $rangeWidth - 1
        ]);
    }

    private static function getSpriteName(string $publicationCode, string $suffix): string
    {
        return implode('-', [
            'edges',
            str_replace('/', '-', $publicationCode),
            $suffix
        ]);
    }

    public function upload($file, $options = array()) : ApiResponse
    {
        putenv('CLOUDINARY_URL=' . $_ENV['CLOUDINARY_URL']);
        return self::$mockedResults['upload'] ?: (new UploadApi())->upload($file, $options);
    }

    public function addTag($tag, $public_ids = array(), $options = array()) : ApiResponse
    {
        putenv('CLOUDINARY_URL=' . $_ENV['CLOUDINARY_URL']);
        return self::$mockedResults['add_tag'] ?: (new UploadApi())->addTag($tag, $public_ids, $options);
    }

    public function generateSprite($tag, $options = array()) : PromiseInterface
    {
        putenv('CLOUDINARY_URL=' . $_ENV['CLOUDINARY_URL']);
        $mockedResults = self::$mockedResults['generate_sprite'][$tag];
        return isset($mockedResults)
            ? new Promise(fn() => $mockedResults)
            : (new UploadApi())->generateSpriteAsync($tag, $options);
    }
}
