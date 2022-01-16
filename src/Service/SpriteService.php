<?php

namespace App\Service;

use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\TranchesPretesSprites;
use App\Entity\Dm\TranchesPretesSpritesUrls;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
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

    public function uploadEdgesAndGenerateSprites(TranchesPretes $edge): array
    {
        $this->uploadEdge($edge);
        $spriteNames = $this->updateTags($edge);
        $createdSprites = $this->generateSprites($edge);
        return [
            'edgesToUpload' => $edge,
            'spriteNames' => $spriteNames,
            'createdSprites' => $createdSprites,
        ];
    }

    private function uploadEdge(TranchesPretes $edgeToUpload): void
    {
        [$country, $magazine] = explode('/', $edgeToUpload->getPublicationcode());

        $this->logger->info("Uploading edge with ID {$edgeToUpload->getId()} and slug {$edgeToUpload->getSlug()}...");
        $this->upload(
            "{$_ENV['EDGES_ROOT']}/$country/gen/$magazine.{$edgeToUpload->getIssuenumber()}.png", [
            'public_id' => $edgeToUpload->getSlug()
        ]);
    }

    private function updateTags(TranchesPretes $edge): array
    {
        $qbDeleteExistingSpriteNames = $this->dmEm->createQueryBuilder();
        $qbDeleteExistingSpriteNames
            ->delete(TranchesPretesSprites::class, 'sprite')
            ->andWhere($qbDeleteExistingSpriteNames->expr()->eq('sprite.idTranche', $edge->getId()));
        $qbDeleteExistingSpriteNames->getQuery()->execute();

        $spriteNames = [];

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
            $this->addTag(
                $spriteName,
                $edge->getSlug()
            );

            $spriteForEdge = new TranchesPretesSprites();
            $this->dmEm->persist($spriteForEdge
                ->setIdTranche($edge)
                ->setSpriteName($spriteName)
                ->setSpriteSize($spriteSize)
            );

            $qbUpdateOtherEdgesInSprite = $this->dmEm->createQueryBuilder()
                ->update(TranchesPretesSprites::class, 'otherEdgeInSprite')
                ->set('otherEdgeInSprite.spriteSize', $spriteSize)
                ->andWhere($qbDeleteExistingSpriteNames->expr()->eq('otherEdgeInSprite.spriteName', $qbDeleteExistingSpriteNames->expr()->literal($spriteName)));
            $qbUpdateOtherEdgesInSprite->getQuery()->execute();

            $this->dmEm->flush();

            $spriteNames[$spriteName] = (object)['size' => $spriteSize];
        }

        return $spriteNames;
    }

    private function generateSprites(TranchesPretes $edge): array
    {
        $qb = ($this->dmEm->createQueryBuilder());
        $qb
            ->select('distinct sprites.spriteName')
            ->from(TranchesPretesSprites::class, 'sprites')
            ->andWhere($qb->expr()->eq('sprites.idTranche', $edge->getId()));

        $spritesWithNoUrl = $qb->getQuery()->getResult();
        foreach ($spritesWithNoUrl as $sprite) {
            ['spriteName' => $spriteName] = $sprite;
            $this->logger->info("Generating sprite for $spriteName...");
            $externalResponse = $this->generateSprite($spriteName);

            $spriteUrl = new TranchesPretesSpritesUrls();

            $this->dmEm->persist(
                $spriteUrl
                    ->setVersion($externalResponse['version'])
                    ->setSpriteName($spriteName)
            );
            $this->dmEm->flush();
        }
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

    public function generateSprite($tag, $options = array()) : ApiResponse
    {
        putenv('CLOUDINARY_URL=' . $_ENV['CLOUDINARY_URL']);
        return self::$mockedResults['generate_sprite'][$tag] ?: (new UploadApi())->generateSprite($tag, $options);
    }
}
