<?php

namespace App\Service;

use App\Entity\Dm\NumerosPopularite;
use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersContributions;
use DateTime;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class EdgeService
{
    private ObjectManager $dmEm;
    private ContributionService $contributionService;
    private SpriteService $spriteService;

    public function __construct(ManagerRegistry $doctrineManagerRegistry, ContributionService $contributionService, SpriteService $spriteService)
    {
        $this->dmEm = $doctrineManagerRegistry->getManager('dm');
        $this->contributionService = $contributionService;
        $this->spriteService = $spriteService;
    }

    /**
     * @throws ORMException
     */
    public function publishEdgeOnDm(array $contributors, string $publicationCode, string $issueNumber): array
    {
        $edgeToPublish = $this->dmEm->getRepository(TranchesPretes::class)->findOneBy([
            'publicationcode' => $publicationCode,
            'issuenumber' => $issueNumber
        ]);
        $isNew = is_null($edgeToPublish);
        if ($isNew) {
            $this->dmEm->persist($edgeToPublish = (new TranchesPretes())
                ->setPublicationcode($publicationCode)
                ->setIssuenumber($issueNumber)
                ->setDateajout(new DateTime())
            );
            $contributions = [];
        } else {
            $contributions = $this->dmEm->getRepository(UsersContributions::class)->findBy([
                'tranche' => $edgeToPublish
            ]);
        }

        [$countryCode, $shortPublicationCode] = explode('/', $edgeToPublish->getPublicationcode());
        /** @var NumerosPopularite $popularity */
        $issuePopularity = $this->dmEm->getRepository(NumerosPopularite::class)->findOneBy([
            'pays' => $countryCode,
            'magazine' => $shortPublicationCode,
            'numero' => $edgeToPublish->getIssuenumber()
        ]);
        $popularity = is_null($issuePopularity) ? 0 : $issuePopularity->getPopularite();

        foreach ($contributors as $contributor) {
            $userId = $contributor['userId'];
            $contribution = $contributor['contribution'];
            /** @var Users $user */
            $user = $this->dmEm->getRepository(Users::class)->find($userId);
            if (empty(array_filter(
                $contributions,
                fn(UsersContributions $existingContribution) =>
                    $existingContribution->getContribution() === $contribution &&
                    $existingContribution->getUser()->getId() === $userId
            ))) {
                $contributions[] = $this->contributionService->persistContribution(
                    $user,
                    $contribution,
                    $popularity,
                    $edgeToPublish
                );
            }
        }
        $this->dmEm->flush();

        if (getenv('APP_ENV') === 'prod') {
            $this->spriteService->uploadEdgesAndGenerateSprites($edgeToPublish->getId());
        }

        return [
            'isNew' => $isNew,
            'edgeId' => $edgeToPublish->getId(),
            'contributors' => array_map(fn(UsersContributions $contribution) => $contribution->getUser()->getId(), $contributions)
        ];
    }
}