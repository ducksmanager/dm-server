<?php
namespace App\Helper;

use App\Entity\Dm\Numeros;
use Doctrine\ORM\EntityManager;

trait collectionUpdateHelper {
    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    private function addOrChangeIssues(EntityManager $em, int $userId, string $country, string $publication, array $issuenumbers, ?string $condition, ?bool $istosell, ?int $purchaseid): array
    {
        $conditionNewIssues = is_null($condition) ? 'possede' : $condition;
        $istosellNewIssues = is_null($istosell) ? false : $istosell;
        $purchaseidNewIssues = is_null($purchaseid) ? -2 : $purchaseid; // TODO allow NULL

        $qb = $em->createQueryBuilder();
        $qb
            ->select('issues')
            ->from(Numeros::class, 'issues')

            ->andWhere($qb->expr()->eq('issues.pays', ':country'))
            ->setParameter(':country', $country)

            ->andWhere($qb->expr()->eq('issues.magazine', ':publication'))
            ->setParameter(':publication', $publication)

            ->andWhere($qb->expr()->in('issues.numero', ':issuenumbers'))
            ->setParameter(':issuenumbers', $issuenumbers)

            ->andWhere($qb->expr()->eq('issues.idUtilisateur', ':userId'))
            ->setParameter(':userId', $userId)

            ->indexBy('issues', 'issues.numero');

        /** @var Numeros[] $existingIssues */
        $existingIssues = $qb->getQuery()->getResult();

        foreach($existingIssues as $existingIssue) {
            if (!is_null($condition)) {
                $existingIssue->setEtat($condition);
            }
            if (!is_null($istosell)) {
                $existingIssue->setAv($istosell);
            }
            if (!is_null($purchaseid)) {
                $existingIssue->setIdAcquisition($purchaseid);
            }
            $em->persist($existingIssue);
        }

        $issueNumbersToCreate = array_diff($issuenumbers, array_keys($existingIssues));
        foreach($issueNumbersToCreate as $issueNumberToCreate) {
            $newIssue = new Numeros();
            $newIssue->setPays($country);
            $newIssue->setMagazine($publication);
            $newIssue->setNumero($issueNumberToCreate);
            $newIssue->setEtat($conditionNewIssues);
            $newIssue->setAv($istosellNewIssues);
            $newIssue->setIdAcquisition($purchaseidNewIssues);
            $newIssue->setIdUtilisateur($userId);
            $newIssue->setDateajout(new \DateTime());

            $em->persist($newIssue);
        }

        $em->flush();
        $em->clear();

        $updateResult = count($existingIssues);
        $creationResult = count($issueNumbersToCreate);

        return [$updateResult, $creationResult];
    }
}
