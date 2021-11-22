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
     * @param int $userId
     * @param string $publicationCode
     * @param array $issueNumbers
     * @param string|null $condition
     * @param bool|null $isOnSale
     * @param int|null $purchaseId
     * @return array
     * @throws Exception
     */
    public function addOrChangeIssues(int $userId, string $publicationCode, array $issueNumbers, ?string $condition, ?bool $isOnSale, ?int $purchaseId): array
    {
        $conditionNewIssues = is_null($condition) ? 'indefini' : $condition;
        $isOnSaleNewIssues = is_null($isOnSale) ? false : $isOnSale;
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
            if (!is_null($isOnSale)) {
                $existingIssue->setIsOnSale($isOnSale);
            }
            if (!is_null($purchaseId)) {
                $existingIssue->setIdAcquisition($purchaseId);
            }
            self::$dmEm->persist($existingIssue);
        }

        [$countryCode, $magazineCode] = explode('/', $publicationCode);

        $issueNumbersToCreate = array_diff($issueNumbers, array_keys($existingIssues));
        foreach($issueNumbersToCreate as $issueNumberToCreate) {
            $this->buildAndPersistIssue($countryCode, $magazineCode, $issueNumberToCreate, $conditionNewIssues, $isOnSaleNewIssues, $purchaseIdNewIssues, $userId);
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
     * @param ?bool[] $areOnSale
     * @param ?int[] $purchaseIds
     * @return array
     * @throws Exception
     */
    public function addOrChangeCopies(int $userId, string $publicationCode, string $issueNumber, array $conditions, array $areOnSale, array $purchaseIds): array
    {
        $this->deleteIssues($userId, $publicationCode, [$issueNumber]);
        [$countryCode, $magazineCode] = explode('/', $publicationCode);
        foreach(array_keys($conditions) as $copyNumber) {
            if (in_array($conditions[$copyNumber], ['missing', 'non_possede'])) {
                continue;
            }
            $condition = $conditions[$copyNumber] ?? 'indefini';
            $isOnSale = $areOnSale[$copyNumber] === 'true';
            $purchaseId = empty($purchaseIds[$copyNumber]) ? -2 : $purchaseIds[$copyNumber]; // TODO allow NULL

            $this->buildAndPersistIssue($countryCode, $magazineCode, $issueNumber, $condition, $isOnSale, $purchaseId, $userId);

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

    private function buildAndPersistIssue(string $countryCode, string $magazine, string $issueNumberToCreate, string $conditionNewIssues, bool $isOnSaleNewIssues, int $purchaseIdNewIssues, int $userId): void
    {
        $newIssue = new Numeros();
        $newIssue->setPays($countryCode);
        $newIssue->setMagazine($magazine);
        $newIssue->setNumero($issueNumberToCreate);
        $newIssue->setEtat($conditionNewIssues);
        $newIssue->setIsOnSale($isOnSaleNewIssues);
        $newIssue->setIdAcquisition($purchaseIdNewIssues);
        $newIssue->setIdUtilisateur($userId);
        $newIssue->setDateajout(new DateTime());

        self::$dmEm->persist($newIssue);
    }
}
