<?php

namespace App\Controller;

use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\TranchesPretesSprites;
use App\Entity\Dm\TranchesPretesSpritesUrls;
use App\Service\SpriteService;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EdgeSpritesController extends AbstractController implements RequiresDmVersionController
{
    private static $SPRITE_SIZES = [ 10, 20, 50, 100, 'full'];
    private static $MAX_SPRITE_SIZE = 100;

    /**
     * @Route(methods={"PUT"}, path="/edgesprites/{edgeID}")
     * @throws ORMException
     */
    public function uploadEdgesAndGenerateSprites(LoggerInterface $logger, SpriteService $spriteService, string $edgeID) {
        $uploadEdgesResult = $this->uploadEdges($logger, $spriteService, $edgeID);
        $updateTagsResult = $this->updateTags($logger, $spriteService, $edgeID);
        $generateSpritesResult = $this->generateSprites($logger, $spriteService, $edgeID);

        return new JsonResponse([
            'edgesToUpload' => json_decode($uploadEdgesResult->getContent(), true),
            'spriteNames' => json_decode($updateTagsResult->getContent(), true),
            'createdSprites' => json_decode($generateSpritesResult->getContent(), true),
        ]);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgesprites/upload/{edgeID}")
     */
    public function uploadEdges(LoggerInterface $logger, SpriteService $spriteService, string $edgeID) {

        $dmEm = $this->getEm('dm');
        $qb = $dmEm->createQueryBuilder();
        $qb
            ->select('edges.publicationcode, edges.issuenumber, edges.slug')
            ->from(TranchesPretes::class, 'edges')
            ->andWhere($qb->expr()->eq('edges.id', ':fromID'))
            ->setParameter(':fromID', $edgeID);
        $edgesToUpload = $qb->getQuery()->getArrayResult();

        foreach($edgesToUpload as $edgeToUpload) {
            [$country, $magazine] = explode('/', $edgeToUpload['publicationcode']);

            $logger->info("Uploading {$edgeToUpload['slug']}...");
            $spriteService->upload(
                "{$_ENV['EDGES_ROOT']}/$country/gen/$magazine.{$edgeToUpload['issuenumber']}.png", [
                    'public_id' => $edgeToUpload['slug']
            ]);
        }

        return new JsonResponse($edgesToUpload);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgesprites/tags/{edgeID}")
     * @throws ORMException
     */
    public function updateTags(LoggerInterface $logger, SpriteService $spriteService, int $edgeID)
    {
        $qbDeleteExistingSpriteNames = $this->getEm('dm')->createQueryBuilder();
        $qbDeleteExistingSpriteNames
            ->delete(TranchesPretesSprites::class, 'sprite')
            ->andWhere($qbDeleteExistingSpriteNames->expr()->eq('sprite.idTranche', $edgeID));
        $qbDeleteExistingSpriteNames->getQuery()->execute();

        $edge = $this->getEm('dm')->getRepository(TranchesPretes::class)->find($edgeID);

        $spriteNames = [];

        foreach(self::$SPRITE_SIZES as $spriteSize) {
            $spriteName = self::getSpriteName($edge->getPublicationcode(), $spriteSize === 'full'
                ? 'full'
                : self::getSpriteRange($edge->getIssuenumber(), $spriteSize)
            );

            if ($spriteSize === 'full') {
                $spriteSize = $this->getEm('dm')->getRepository(TranchesPretes::class)->count([
                    'publicationcode' => $edge->getPublicationcode()
                ]);
                if ($spriteSize > self::$MAX_SPRITE_SIZE) {
                    $logger->info("Not creating a full sprite for publication {$edge->getPublicationcode()} : sprite size is too big ($spriteSize)");
                    continue;
                }
            }
            else {
                $spriteSize = $this->getEm('dm')->getRepository(TranchesPretesSprites::class)->count([
                    'spriteName' => $spriteName
                ]) + 1;
            }

            $logger->info("Adding tag $spriteName on {$edge->getSlug()}");
            $spriteService->add_tag(
                $spriteName,
                $edge->getSlug()
            );

            $spriteForEdge = new TranchesPretesSprites();
            $this->getEm('dm')->persist($spriteForEdge
                ->setIdTranche($edge)
                ->setSpriteName($spriteName)
                ->setSpriteSize($spriteSize)
            );

            $qbUpdateOtherEdgesInSprite = $this->getEm('dm')->createQueryBuilder()
                ->update(TranchesPretesSprites::class, 'otherEdgeInSprite')
                ->set('otherEdgeInSprite.spriteSize', $spriteSize)
                ->andWhere($qbDeleteExistingSpriteNames->expr()->eq('otherEdgeInSprite.spriteName', $qbDeleteExistingSpriteNames->expr()->literal($spriteName)));
            $qbUpdateOtherEdgesInSprite->getQuery()->execute();

            $this->getEm('dm')->flush();

            $spriteNames[$spriteName] = (object) ['size' => $spriteSize];
        }

        return new JsonResponse($spriteNames);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgesprites/sprites/{edgeID}")
     * @throws ORMException
     */
    public function generateSprites(LoggerInterface $logger, SpriteService $spriteService, int $edgeID) {

        $dmEm = $this->getEm('dm');
//        $qbExistingUrls = $dmEm->createQueryBuilder();
//        $qbExistingUrls
//            ->select('sprites_urls.spriteName')
//            ->from(TranchesPretesSpritesUrls::class, 'sprites_urls');

        $qb = $dmEm->createQueryBuilder();
        $qb
            ->select('distinct sprites.spriteName')
            ->from(TranchesPretesSprites::class, 'sprites')
//            ->andWhere($qb->expr()->notIn('sprites.spriteName', $qbExistingUrls->getDQL()))
            ->andWhere($qb->expr()->eq('sprites.idTranche', $edgeID));

        $spritesWithNoUrl = $qb->getQuery()->getResult();
        foreach($spritesWithNoUrl as $sprite) {
            ['spriteName' => $spriteName] = $sprite;
            $logger->info("Generating sprite for $spriteName...");
            $externalResponse = $spriteService->generate_sprite($spriteName);

            $spriteUrl = new TranchesPretesSpritesUrls();

            $dmEm = $this->getEm('dm');
            $dmEm->persist(
                $spriteUrl
                    ->setVersion($externalResponse['version'])
                    ->setSpriteName($spriteName)
            );
            $dmEm->flush();
        }
        return new JsonResponse(array_map(function($spriteWithNoUrl) {
            return $spriteWithNoUrl['spriteName'];
        }, $spritesWithNoUrl));
    }

    private static function getSpriteRange(string $issueNumber, int $rangeWidth): string
    {
        $issueNumberInt = (int)$issueNumber;
        return implode('-', [
            $issueNumberInt - ($issueNumberInt - 1) % $rangeWidth,
            ($issueNumberInt - ($issueNumberInt - 1) % $rangeWidth) + $rangeWidth - 1
        ]);
    }

    public static function getSpriteName(string $publicationCode, string $suffix): string
    {
        return implode('-', [
            'edges',
            str_replace('/', '-', $publicationCode),
            $suffix
        ]);
    }
}
