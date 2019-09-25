<?php
namespace App\Service;

use App\Entity\Dm\Bouquineries;
use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersContributions;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Exception;

class ContributionService {

    private static $dmEm;

    public function __construct(ManagerRegistry $doctrineManagerRegistry)
    {
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');
    }

    /**
     * @param Users $user
     * @param string $contributionType
     * @param int $newPoints
     * @param TranchesPretes|null $edgeToPublish
     * @param Bouquineries|null $bookStoreToPublish
     * @return UsersContributions
     * @throws Exception
     */
    public function persistContribution(Users $user, string $contributionType, int $newPoints, ?TranchesPretes $edgeToPublish = null, ?Bouquineries $bookStoreToPublish = null): UsersContributions
    {
        /** @var QueryBuilder $qb */
        $qb = self::$dmEm->createQueryBuilder();
        $qb->select('sum(uc.pointsNew)')
            ->from(UsersContributions::class, 'uc')
            ->where('uc.user = :user and uc.contribution = :contribution')
            ->setParameter(':user', $user)
            ->setParameter(':contribution', $contributionType);
        $currentUserPoints = (int)$qb->getQuery()->getSingleScalarResult();

        $contribution = new UsersContributions();
        $contribution
            ->setTranche($edgeToPublish)
            ->setUser($user)
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
        self::$dmEm->persist($contribution);
        return $contribution;
    }
}
