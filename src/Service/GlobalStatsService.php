<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

class GlobalStatsService
{
    private static EntityManager $dmEm;

    public function __construct(ManagerRegistry $doctrineManagerRegistry)
    {
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');
    }

    public function getUsersQuickStats(array $userIds): array
    {
        $userIdsList = implode(',', $userIds);

        $userQuickStatsQuery = "
            select
               u.ID AS userId,
               u.username,
               count(distinct Pays) AS numberOfCountries,
               count(distinct concat(Pays, '/', Magazine)) as numberOfPublications,
               count(*) as numberOfIssues
            from users u
            inner join numeros on numeros.ID_Utilisateur = u.ID
            where u.ID IN (:userIds)
            group by u.ID";

        return array_map(fn(array $result) => array_merge($result, [
            'userId' => (int)$result['userId'],
            'numberOfCountries' => (int)$result['numberOfCountries'],
            'numberOfPublications' => (int)$result['numberOfPublications'],
            'numberOfIssues' => (int)$result['numberOfIssues']
        ]), self::$dmEm->getConnection()->fetchAllAssociative($userQuickStatsQuery, ['userIds' => $userIdsList]));

    }

    public function getUsersPoints(array $userIds): array
    {
        $userIdsList = implode(',', $userIds);

        $userPointsQuery = "
            select type_contribution.contribution, ids_users.ID_User, ifnull(contributions_utilisateur.points_total, 0) as points_total
            from (
                select 'Photographe' as contribution union
                select 'Createur' as contribution union
                select 'Duckhunter' as contribution
            ) as type_contribution
            join (
                SELECT ID AS ID_User
                FROM users
                WHERE ID IN (:userIds)
            ) AS ids_users
            left join (
                SELECT uc.ID_User, uc.contribution, sum(points_new) as points_total
                FROM users_contributions uc
                GROUP BY uc.ID_User, uc.contribution
            ) as contributions_utilisateur
                ON type_contribution.contribution = contributions_utilisateur.contribution
               AND ids_users.ID_User = contributions_utilisateur.ID_user";

        return array_map(fn(array $result) => array_merge($result, [
            'points_total' => (int)$result['points_total'],
            'ID_User' => (int)$result['ID_User']
        ]), self::$dmEm->getConnection()->fetchAllAssociative($userPointsQuery, ['userIds' => $userIdsList]));

    }
}
