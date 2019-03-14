<?php

namespace App\Controller;

use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\TranchesPretesSprites;
use App\Entity\Dm\TranchesPretesSpritesUrls;
use Symfony\Component\Routing\Annotation\Route;

class EdgeSpritesController extends AbstractController implements RequiresDmVersionController, RequiresDmUserController
{

    /**
     * @Route(methods={"POST"}, path="/edgesprites/tags")
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateTags()
    {
        $TAGGABLE_ASSETS_LIMIT = 1000;

        $allSpritesAndEdges = $this->getEm('dm')->getRepository(TranchesPretesSprites::class)->findBy([], ['spriteName' => 'ASC']);

        $slugsPerSprite = [];
        foreach ($allSpritesAndEdges as $spriteAndEdge) {
            $slugsPerSprite[$spriteAndEdge->getSpriteName()][] = $spriteAndEdge->getIdTranche()->getSlug();
        }

        /** @var TranchesPretes[] $slug */
        foreach ($slugsPerSprite as $spriteName => $slug) {
            $numberOfSlugs = count($slug);
            for ($i = 0; $i < $numberOfSlugs; $i += $TAGGABLE_ASSETS_LIMIT) {
                $tagCreationResponse = \Cloudinary\Uploader::add_tag(
                    $spriteName,
                    array_slice($slug, $i, $TAGGABLE_ASSETS_LIMIT - 1)
                );

                $spriteUrl = new TranchesPretesSpritesUrls();
                $this->getEm('dm')->persist($spriteUrl
                    ->setSpriteName($spriteName)
                    ->setVersion($tagCreationResponse->version)
                );
            }
        }

        $this->getEm('dm')->flush();
    }
}
