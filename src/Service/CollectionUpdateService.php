<?php
namespace App\Service;

use App\Entity\Dm\Numeros;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Exception;

class CollectionUpdateService {

    private static $dmEm;

    public function __construct(ManagerRegistry $doctrineManagerRegistry)
    {
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');
    }


    /**
     * @param int $userId
     * @param string $publicationCode
     * @param array $issueNumbers
     * @param string|null $condition
     * @param bool|null $istosell
     * @param int|null $purchaseId
     * @return array
     * @throws Exception
     */
    public function addOrChangeIssues(int $userId, string $publicationCode, array $issueNumbers, ?string $condition, ?bool $istosell, ?int $purchaseId): array
    {
        $conditionNewIssues = is_null($condition) ? 'possede' : $condition;
        $istosellNewIssues = is_null($istosell) ? false : $istosell;
        $purchaseIdNewIssues = is_null($purchaseId) ? -2 : $purchaseId; // TODO allow NULL

        /** @var QueryBuilder $qb */
        $qb = self::$dmEm->createQueryBuilder();
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
            self::$dmEm->persist($existingIssue);
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
            $newIssue->setDateajout(new DateTime());

            self::$dmEm->persist($newIssue);
        }

        self::$dmEm->flush();
        self::$dmEm->clear();

        $updateResult = count($existingIssues);
        $creationResult = count($issueNumbersToCreate);

        return [$updateResult, $creationResult];
    }
}
