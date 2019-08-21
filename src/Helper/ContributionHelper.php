<?php
namespace App\Helper;

use App\Entity\Dm\Bouquineries;
use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\UsersContributions;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

class ContributionHelper {

    /**
     * @param EntityManager $dmEm
     * @param int $userId
     * @param string $contributionType
     * @param int $newPoints
     * @param TranchesPretes|null $edgeToPublish
     * @param Bouquineries|null $bookStoreToPublish
     * @return UsersContributions
     * @throws ORMException
     */
    public static function persistContribution(EntityManager $dmEm, int $userId, string $contributionType, int $newPoints, ?TranchesPretes $edgeToPublish = null, ?Bouquineries $bookStoreToPublish = null): UsersContributions
    {
        $qb = $dmEm->createQueryBuilder();
        $qb->select('sum(uc.pointsNew)')
            ->from(UsersContributions::class, 'uc')
            ->where('uc.idUser = :idUser and uc.contribution = :contribution')
            ->setParameter(':idUser', $userId)
            ->setParameter(':contribution', $contributionType);
        $currentUserPoints = $qb->getQuery()->getSingleScalarResult();

        $contribution = new UsersContributions();
        $contribution
            ->setTranche($edgeToPublish)
            ->setIdUser($userId)
            ->setContribution($contributionType)
            ->setDate(new DateTime())
            ->setPointsNew($newPoints)
            ->setPointsTotal($currentUserPoints + $newPoints);
        if (!is_null($edgeToPublish)) {
            $contribution->setTranche($edgeToPublish);
        }
        if (!is_null($bookStoreToPublish)) {
            $contribution->setBookstore($bookStoreToPublish);
        }
        $dmEm->persist($contribution);
        return $contribution;
    }
}
