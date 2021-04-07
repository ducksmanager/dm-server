<?php
namespace App\Service;

use App\Entity\Dm\Bouquineries;
use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersContributions;
use DateTime;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ContributionService {

    private static ObjectManager $dmEm;

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

    public function getMedalPoints(array $userIds): array
    {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('contribution_external_name', 'contribution')
            ->addScalarResult('userId', 'userId', 'integer')
            ->addScalarResult('totalPoints', 'totalPoints', 'integer');

        $query = self::$dmEm->createNativeQuery("
            select contributionType.contribution_external_name, userIds.userId, ifnull(userContributions.totalPoints, 0) as totalPoints
            from (
                select 'Photographe' as contribution, 'edge_photographer' as contribution_external_name union
                select 'Createur' as contribution, 'edge_designer' as contribution_external_name union
                select 'Duckhunter' as contribution, 'duckhunter' as contribution_external_name
            ) as contributionType
            join (
                SELECT ID AS userId
                FROM users
                WHERE ID IN (:userIds)
            ) AS userIds
            left join (
                SELECT uc.ID_User AS userId, uc.contribution, sum(points_new) as totalPoints
                FROM users_contributions uc
                GROUP BY userId, uc.contribution
            ) as userContributions
                ON contributionType.contribution = userContributions.contribution
               AND userIds.userId = userContributions.userId
        ", $rsm);

        $query->setParameter(':userIds', $userIds);

        $results = $query->getArrayResult();

        $groupedResults = [];

        foreach($results as $result) {
            if (!array_key_exists($result['userId'], $groupedResults)) {
                $groupedResults[$result['userId']] = [];
            }
            $groupedResults[$result['userId']][] = $result;
        }

        return $groupedResults;
    }
}
