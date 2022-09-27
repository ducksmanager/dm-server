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
use Symfony\Component\HttpKernel\KernelInterface;

class EdgeService
{
    private ObjectManager $dmEm;
    private KernelInterface $kernel;
    private ContributionService $contributionService;
    private SpriteService $spriteService;

    public function __construct(ManagerRegistry $doctrineManagerRegistry, KernelInterface $kernel, ContributionService $contributionService, SpriteService $spriteService)
    {
        $this->dmEm = $doctrineManagerRegistry->getManager('dm');
        $this->kernel = $kernel;
        $this->contributionService = $contributionService;
        $this->spriteService = $spriteService;
    }

    /**
     * @throws ORMException
     */
    public function publishEdgeOnDm(array $contributors, string $publicationCode, string $issueNumber): array
    {
        /** @var TranchesPretes $edgeToPublish */
        $edgeToPublish = $this->dmEm->getRepository(TranchesPretes::class)->findOneBy([
            'publicationcode' => $publicationCode,
            'issuenumber' => $issueNumber
        ]);
        $isNew = is_null($edgeToPublish);
        if ($isNew) {
            $edgeToPublish = (new TranchesPretes())
                ->setPublicationcode($publicationCode)
                ->setIssuenumber($issueNumber)
                ->setDateajout(new DateTime());

            $this->dmEm->persist($edgeToPublish);

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
        $edgeId = $edgeToPublish->getId();
        $this->dmEm->clear();

        return [
            'isNew' => $isNew,
            'edgeId' => $edgeToPublish->getId(),
            'contributors' => array_map(fn(UsersContributions $contribution) => $contribution->getUser()->getId(), $contributions)
        ];
    }
}
