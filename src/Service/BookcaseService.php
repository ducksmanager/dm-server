<?php
namespace App\Service;

use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\NumerosPopularite;
use App\Entity\Dm\Users;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;

class BookcaseService
{
    /** @var EntityManager */
    private static $dmEm;

    public function __construct(ManagerRegistry $doctrineManagerRegistry)
    {
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');
    }

    public function getUserBookcase(Users $user) : array
    {
        $query = "
            SELECT numeros.ID AS id,
                   numeros.Pays AS countryCode,
                   numeros.Magazine AS magazineCode,
                   numeros.Numero AS issueNumber,
                   IFNULL(reference.NumeroReference, numeros.Numero_nospace) AS issueNumberReference,
                   tp.ID AS edgeId,
                   tp.DateAjout AS creationDate,
                   IF(tp.ID IS NULL, '', GROUP_CONCAT(
                       IF(sprites.Sprite_name is null, '',
                          JSON_OBJECT('name', sprites.Sprite_name, 'version', sprites.Version, 'size', sprites.Sprite_size))
                       ORDER BY sprites.Sprite_size ASC
                   )) AS sprites
            FROM numeros
            LEFT JOIN tranches_doublons reference
                 ON numeros.Pays = reference.Pays
                AND numeros.Magazine = reference.Magazine
                AND numeros.Numero_nospace = reference.Numero
            LEFT JOIN tranches_pretes tp
                ON  CONCAT(numeros.Pays, '/', numeros.Magazine) = tp.publicationcode
                AND IFNULL(reference.NumeroReference, numeros.Numero_nospace) = tp.issuenumber
            LEFT JOIN (
                SELECT sprites.ID_Tranche, sprites.sprite_name, sprites.Sprite_size, sprite_urls.Version
                FROM tranches_pretes_sprites sprites
                INNER JOIN tranches_pretes_sprites_urls sprite_urls
                    ON sprites.Sprite_name = sprite_urls.Sprite_name
            ) AS sprites
                ON sprites.ID_Tranche = tp.ID
            WHERE ID_Utilisateur = ?";

        if ($user->getBibliothequeAfficherdoubles()) {
            $query.=' GROUP BY numeros.ID';
        }
        else {
            $query.=' GROUP BY numeros.Pays, numeros.Magazine, numeros.Numero';
        }

        return self::$dmEm->getConnection()->fetchAllAssociative($query, [$user->getId()]);
    }

    public function getLastPublicationPosition(int $userId) : ?int {
        $qb = self::$dmEm->createQueryBuilder();
        $qb
            ->select('max(sorts.ordre) as max')
            ->from(BibliothequeOrdreMagazines::class, 'sorts')
            ->andWhere($qb->expr()->eq('sorts.idUtilisateur', ':userId'))
            ->setParameter(':userId', $userId);

        try {
            $maxSort = $qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            $maxSort = null;
        }
        finally {
            return (int) $maxSort;
        }
    }

    public function getBookcaseSorting(string $username, bool $isCurrentUser): ?array
    {
        /** @var Users $user */
        $user = self::$dmEm->getRepository(Users::class)->findOneBy(['username' => $username]);
        if (!$isCurrentUser && (!$user || !$user->getAccepterpartage())) {
            return null;
        }
        $maxSort = $this->getLastPublicationPosition($user->getId());

        $qbMissingSorts = (self::$dmEm->createQueryBuilder())
            ->select('distinct concat(issues.pays, \'/\', issues.magazine) AS missing_publication_code')
            ->from(Numeros::class, 'issues')

            ->andWhere('concat(issues.pays, \'/\', issues.magazine) not in (select sorts.publicationcode from '.BibliothequeOrdreMagazines::class.' sorts where sorts.idUtilisateur = :userId)')
            ->andWhere('issues.idUtilisateur = :userId')
            ->setParameter(':userId', $user->getId())

            ->orderBy(new OrderBy('missing_publication_code', 'ASC'));

        $missingSorts = $qbMissingSorts->getQuery()->getArrayResult();
        foreach($missingSorts as $missingSort) {
            $sort = new BibliothequeOrdreMagazines();
            $sort->setPublicationcode($missingSort['missing_publication_code']);
            $sort->setOrdre(++$maxSort);
            $sort->setIdUtilisateur($user->getId());
            self::$dmEm->persist($sort);
        }
        self::$dmEm->flush();

        $sorts = self::$dmEm->getRepository(BibliothequeOrdreMagazines::class)->findBy(
            ['idUtilisateur' => $user->getId()],
            ['ordre' => 'ASC']
        );

        return array_map(function(BibliothequeOrdreMagazines $sort) {
            return $sort->getPublicationcode();
        }, $sorts);
    }

    /**
     * @deprecated
     */
    function getBookcaseTextures(string $username) : array {
        /** @var Users $user */
        $user = self::$dmEm->getRepository(Users::class)->findOneBy(['username' => $username]);
        return [
            'bookcase' => "{$user->getBibliothequeTexture1()}/{$user->getBibliothequeSousTexture1()}",
            'bookshelf' => "{$user->getBibliothequeTexture2()}/{$user->getBibliothequeSousTexture2()}"
        ];
    }

    function getBookcaseOptions(string $username) : array {
        /** @var Users $user */
        $user = self::$dmEm->getRepository(Users::class)->findOneBy(['username' => $username]);
        return [
            'textures' => [
                'bookcase' => "{$user->getBibliothequeTexture1()}/{$user->getBibliothequeSousTexture1()}",
                'bookshelf' => "{$user->getBibliothequeTexture2()}/{$user->getBibliothequeSousTexture2()}"
            ],
            'showAllCopies' => $user->getBibliothequeAfficherdoubles(),
        ];
    }

    /**
     * @deprecated
     */
    function updateBookcaseTextures(string $username, array $textures) : void {
        /** @var Users $user */
        $user = self::$dmEm->getRepository(Users::class)->findOneBy(['username' => $username]);
        [, $bookcaseTexture] = explode('/', $textures['bookcase']);
        [, $bookshelfTexture] = explode('/', $textures['bookshelf']);
        $user->setBibliothequeSousTexture1($bookcaseTexture);
        $user->setBibliothequeSousTexture2($bookshelfTexture);

        self::$dmEm->persist($user);
        self::$dmEm->flush();
    }

    function updateBookcaseOptions(string $username, array $options) : void {
        /** @var Users $user */
        $user = self::$dmEm->getRepository(Users::class)->findOneBy(['username' => $username]);
        [, $bookcaseTexture] = explode('/', $options['textures']['bookcase']);
        [, $bookshelfTexture] = explode('/', $options['textures']['bookshelf']);
        $showAllCopies = $options['showAllCopies'] ?? true;
        $user->setBibliothequeSousTexture1($bookcaseTexture);
        $user->setBibliothequeSousTexture2($bookshelfTexture);
        $user->setBibliothequeAfficherdoubles($showAllCopies);

        self::$dmEm->persist($user);
        self::$dmEm->flush();
    }

    function getCollectionPopularIssues(int $userId) : array {
        /** @var QueryBuilder */
        $qb = self::$dmEm->createQueryBuilder();
        $qb->select('issue_pop.pays AS country, issue_pop.magazine AS magazine, issue_pop.numero AS issueNumber, issue_pop.popularite AS popularity')
        ->from(NumerosPopularite::class, 'issue_pop')
        ->join(Numeros::class, 'issue', Join::WITH, 'issue_pop.pays = issue.pays AND issue_pop.magazine = issue.magazine AND issue_pop.numero = issue.numero ')
        ->andWhere($qb->expr()->eq('issue.idUtilisateur', ':userId'))
        ->setParameter('userId', $userId)
        ->orderBy('issue_pop.popularite', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
