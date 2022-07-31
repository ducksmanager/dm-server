<?php
namespace App\Service;

use App\Entity\Dm\Numeros;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use Exception;

class CollectionUpdateService {

    private static ObjectManager $dmEm;

    public function __construct(ManagerRegistry $doctrineManagerRegistry)
    {
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');
    }

    /**
     * @throws Exception
     */
    public function addOrChangeIssues(int $userId, string $publicationCode, array $issueNumbers, ?string $condition, ?bool $isToSell, ?bool $isToRead, ?int $purchaseId): array
    {
        $conditionNewIssues = is_null($condition) ? 'indefini' : $condition;
        $isToSellNewIssues = is_null($isToSell) ? false : $isToSell;
        $isToReadNewIssues = is_null($isToRead) ? false : $isToRead;
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
            if (!is_null($isToSell)) {
                $existingIssue->setAv($isToSell);
            }
            if (!is_null($isToRead)) {
                $existingIssue->setALire($isToRead);
            }
            if (!is_null($purchaseId)) {
                $existingIssue->setIdAcquisition($purchaseId);
            }
            self::$dmEm->persist($existingIssue);
        }

        [$countryCode, $magazineCode] = explode('/', $publicationCode);

        $issueNumbersToCreate = array_diff($issueNumbers, array_keys($existingIssues));
        foreach($issueNumbersToCreate as $issueNumberToCreate) {
            $this->buildAndPersistIssue($countryCode, $magazineCode, $issueNumberToCreate, $conditionNewIssues, $isToSellNewIssues, $isToReadNewIssues, $purchaseIdNewIssues, $userId);
        }

        self::$dmEm->flush();
        self::$dmEm->clear();

        $updateResult = count($existingIssues);
        $creationResult = count($issueNumbersToCreate);

        return [$updateResult, $creationResult];
    }

    /**
     * @param int $userId
     * @param string $publicationCode
     * @param string $issueNumber
     * @param ?string[] $conditions
     * @param ?bool[] $areToSell
     * @param ?bool[] $areToRead
     * @param ?int[] $purchaseIds
     * @return array
     * @throws Exception
     */
    public function addOrChangeCopies(int $userId, string $publicationCode, string $issueNumber, array $conditions, array $areToSell, array $areToRead, array $purchaseIds): array
    {
        $this->deleteIssues($userId, $publicationCode, [$issueNumber]);
        [$countryCode, $magazineCode] = explode('/', $publicationCode);
        foreach(array_keys($conditions) as $copyNumber) {
            if (in_array($conditions[$copyNumber], ['missing', 'non_possede'])) {
                continue;
            }
            $condition = $conditions[$copyNumber] ?? 'indefini';
            $isToSell = $areToSell[$copyNumber] ?? false;
            $isToRead = $areToRead[$copyNumber] ?? false;
            if ($areToRead[$copyNumber] === 'do_not_change') {
                $isToRead = false;
            }
            else {
                $isToRead = $areToRead[$copyNumber] ?? false;
            }
            $purchaseId = empty($purchaseIds[$copyNumber]) ? -2 : $purchaseIds[$copyNumber]; // TODO allow NULL

            $this->buildAndPersistIssue($countryCode, $magazineCode, $issueNumber, $condition, $isToSell, $isToRead, $purchaseId, $userId);

        }
        self::$dmEm->flush();
        return [0, 0];
    }

    public function deleteIssues(int $userId, string $publicationCode, array $issueNumbers) : void
    {
        /** @var QueryBuilder $qb */
        $qb = self::$dmEm->createQueryBuilder();
        $qb
            ->delete(Numeros::class, 'issues')
            ->andWhere($qb->expr()->eq($qb->expr()->concat('issues.pays', $qb->expr()->literal('/'), 'issues.magazine'), ':publicationCode'))
            ->setParameter(':publicationCode', $publicationCode)
            ->andWhere($qb->expr()->in('issues.numero', ':issueNumbers'))
            ->setParameter(':issueNumbers', $issueNumbers)
            ->andWhere($qb->expr()->in('issues.idUtilisateur', ':userId'))
            ->setParameter(':userId', $userId);

        $qb->getQuery()->execute();
    }

    private function buildAndPersistIssue(
        string $countryCode,
        string $magazine,
        string $issueNumberToCreate,
        string $conditionNewIssues,
        bool   $isToSellNewIssues,
        bool   $isToReadNewIssues,
        int    $purchaseIdNewIssues,
        int    $userId
    ): void
    {
        $newIssue = new Numeros();
        $newIssue->setPays($countryCode);
        $newIssue->setMagazine($magazine);
        $newIssue->setNumero($issueNumberToCreate);
        $newIssue->setEtat($conditionNewIssues);
        $newIssue->setAv($isToSellNewIssues);
        $newIssue->setALire($isToReadNewIssues);
        $newIssue->setIdAcquisition($purchaseIdNewIssues);
        $newIssue->setIdUtilisateur($userId);
        $newIssue->setDateajout(new DateTime());

        self::$dmEm->persist($newIssue);
    }
}
