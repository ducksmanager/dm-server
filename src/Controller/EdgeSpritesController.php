<?php

namespace App\Controller;

use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\TranchesPretesSprites;
use App\Entity\Dm\TranchesPretesSpritesUrls;
use App\Helper\SpriteHelper;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\OrderBy;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EdgeSpritesController extends AbstractController implements RequiresDmVersionController
{
    static $SPRITE_SIZES = [ 10, 20, 50, 100, 'full'];

    /**
     * @Route(methods={"PUT"}, path="/edgesprites/from/{fromEdgeID}")
     * @throws ORMException
     */
    public function uploadEdgesAndGenerateSprites(LoggerInterface $logger, string $fromEdgeID) {
        $uploadEdgesResult = $this->uploadEdges($logger, $fromEdgeID);
        $updateTagsResult = $this->updateTags($logger, $fromEdgeID);
        $generateSpritesResult = $this->generateSprites($logger, $fromEdgeID);

        return new JsonResponse([
            'edgesToUpload' => (array) json_decode($uploadEdgesResult->getContent()),
            'spriteNames' => (array) json_decode($updateTagsResult->getContent()),
            'createdSprites' => (array) json_decode($generateSpritesResult->getContent()),
        ]);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgesprites/upload/from/{fromEdgeID}")
     */
    public function uploadEdges(LoggerInterface $logger, string $fromEdgeID) {

        $dmEm = $this->getEm('dm');
        $qb = $dmEm->createQueryBuilder();
        $qb
            ->select('edges.publicationcode, edges.issuenumber, edges.slug')
            ->from(TranchesPretes::class, 'edges')
            ->andWhere($qb->expr()->gte('edges.id', ':fromID'))
            ->setParameter(':fromID', $fromEdgeID);
        $edgesToUpload = $qb->getQuery()->getArrayResult();

        foreach($edgesToUpload as $edgeToUpload) {
            [$country, $magazine] = explode('/', $edgeToUpload['publicationcode']);

            $logger->info("Uploading {$edgeToUpload['slug']}...");
            SpriteHelper::upload(
                "{$_ENV['EDGES_ROOT']}/$country/gen/$magazine.{$edgeToUpload['issuenumber']}.png", [
                    'public_id' => $edgeToUpload['slug']
            ]);
        }

        return new JsonResponse($edgesToUpload);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgesprites/tags/from/{fromEdgeID}")
     * @throws ORMException
     */
    public function updateTags(LoggerInterface $logger, int $fromEdgeID)
    {

        $TAGGABLE_ASSETS_LIMIT = 1000;

        $qbDeleteExistingSpriteNames = $this->getEm('dm')->createQueryBuilder();
        $qbDeleteExistingSpriteNames
            ->delete(TranchesPretesSprites::class, 'sprite')
            ->andWhere($qbDeleteExistingSpriteNames->expr()->gte('sprite.idTranche', $fromEdgeID));
        $qbDeleteExistingSpriteNames->getQuery()->execute();

        $edge = $this->getEm('dm')->getRepository(TranchesPretes::class)->find($fromEdgeID);

        $spriteNames = [];

        foreach(self::$SPRITE_SIZES as $spriteSize) {
            if ($spriteSize === 'full') {
                $spriteName = self::getSpriteName($edge->getPublicationcode(), 'full');
                $spriteSize = $this->getEm('dm')->getRepository(TranchesPretes::class)->count([
                    'publicationcode' => $edge->getPublicationcode()
                ]);
            }
            else {
                $spriteName = self::getSpriteName($edge->getPublicationcode(), self::getSpriteRange($edge->getIssuenumber(), $spriteSize));
            }

            $spriteForEdge = new TranchesPretesSprites();
            $this->getEm('dm')->persist($spriteForEdge
                ->setIdTranche($edge)
                ->setSpriteName($spriteName)
                ->setSpriteSize($spriteSize)
            );
            $logger->info("Adding tag $spriteName on {$edge->getSlug()}");
            SpriteHelper::add_tag(
                $spriteName,
                $edge->getSlug()
            );
            $this->getEm('dm')->flush();

            $spriteNames[] = $spriteName;
        }


        $qb = $this->getEm('dm')->createQueryBuilder();
        $qb
            ->select('sprite')
            ->from(TranchesPretesSprites::class, 'sprite')
            ->andWhere($qb->expr()->gte('sprite.idTranche', $fromEdgeID))
            ->orderBy(new OrderBy('sprite.spriteName', 'ASC'));

        /** @var TranchesPretesSprites[] $allSpritesAndEdges */
        $allSpritesAndEdges = $qb->getQuery()->getResult();

        $slugsPerSprite = [];
        foreach ($allSpritesAndEdges as $spriteAndEdge) {
            $slugsPerSprite[$spriteAndEdge->getSpriteName()][] = $spriteAndEdge->getIdTranche()->getSlug();
        }

        /** @var string[] $edgeSlugs */
        foreach ($slugsPerSprite as $spriteName => $edgeSlugs) {
            $numberOfEdges = count($edgeSlugs);
            $logger->info("Adding tag $spriteName on $numberOfEdges edges");
            for ($i = 0; $i < $numberOfEdges; $i += $TAGGABLE_ASSETS_LIMIT) {
                SpriteHelper::add_tag(
                    $spriteName,
                    array_slice($edgeSlugs, $i, $TAGGABLE_ASSETS_LIMIT - 1)
                );
            }
        }

        return new JsonResponse($spriteNames);
    }

    /**
     * @Route(methods={"PUT"}, path="/edgesprites/sprites/from/{fromEdgeID}")
     * @throws ORMException
     */
    public function generateSprites(LoggerInterface $logger, int $fromEdgeID) {

        $dmEm = $this->getEm('dm');
        $qbExistingUrls = $dmEm->createQueryBuilder();
        $qbExistingUrls
            ->select('sprites_urls.spriteName')
            ->from(TranchesPretesSpritesUrls::class, 'sprites_urls');

        $qb = $dmEm->createQueryBuilder();
        $qb
            ->select('distinct sprites.spriteName')
            ->from(TranchesPretesSprites::class, 'sprites')
            ->andWhere($qb->expr()->notIn('sprites.spriteName', $qbExistingUrls->getDQL()))
            ->andWhere($qb->expr()->notLike('sprites.spriteName', ':fullSprite'))
            ->andWhere($qb->expr()->gte('sprites.idTranche', $fromEdgeID))
            ->setParameter(':fullSprite', '%-full');

        $spritesWithNoUrl = $qb->getQuery()->getResult();
        foreach($spritesWithNoUrl as $sprite) {
            ['spriteName' => $spriteName] = $sprite;
            $logger->info("Generating sprite for $spriteName...");
            $externalResponse = SpriteHelper::generate_sprite($spriteName);

            $spriteUrl = new TranchesPretesSpritesUrls();

            $dmEm = $this->getEm('dm');
            $dmEm->persist(
                $spriteUrl
                    ->setVersion($externalResponse['version'])
                    ->setSpriteName($spriteName)
            );
            $dmEm->flush();
        }
        return new JsonResponse($spritesWithNoUrl);
    }

    static function getSpriteRange(string $issueNumber, int $rangeWidth): string
    {
        $issueNumberInt = (int)$issueNumber;
        return implode('-', [
            $issueNumberInt - ($issueNumberInt - 1) % $rangeWidth,
            ($issueNumberInt - ($issueNumberInt - 1) % $rangeWidth) + $rangeWidth - 1
        ]);
    }

    static function getSpriteName(string $publicationCode, string $suffix): string
    {
        return implode('-', [
            'edges',
            str_replace('/', '-', $publicationCode),
            $suffix
        ]);
    }
}
