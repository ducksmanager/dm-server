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
    private function addOrChangeIssues(EntityManager $em, int $userId, string $publicationCode, array $issueNumbers, ?string $condition, ?bool $istosell, ?int $purchaseId): array
    {
        $conditionNewIssues = is_null($condition) ? 'possede' : $condition;
        $istosellNewIssues = is_null($istosell) ? false : $istosell;
        $purchaseIdNewIssues = is_null($purchaseId) ? -2 : $purchaseId; // TODO allow NULL

        $qb = $em->createQueryBuilder();
        $qb
            ->select('issues')
            ->from(Numeros::class, 'issues')

            ->andWhere($qb->expr()->eq($qb->expr()->concat('issues.pays',  $qb->expr()->literal('/'), 'issues.magazine'), ':publicationCode'))
            ->setParameter(':publicationCode', $publicationCode)

            ->andWhere($qb->expr()->in('issues.numero', ':issueNumbers'))
            ->setParameter(':issueNumbers', $issueNumbers)

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
            if (!is_null($purchaseId)) {
                $existingIssue->setIdAcquisition($purchaseId);
            }
            $em->persist($existingIssue);
        }

        [$countryCode, $magazine] = explode('/', $publicationCode);

        $issueNumbersToCreate = array_diff($issueNumbers, array_keys($existingIssues));
        foreach($issueNumbersToCreate as $issueNumberToCreate) {
            $newIssue = new Numeros();
            $newIssue->setPays($countryCode);
            $newIssue->setMagazine($magazine);
            $newIssue->setNumero($issueNumberToCreate);
            $newIssue->setEtat($conditionNewIssues);
            $newIssue->setAv($istosellNewIssues);
            $newIssue->setIdAcquisition($purchaseIdNewIssues);
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
