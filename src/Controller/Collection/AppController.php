<?php

namespace App\Controller\Collection;

use App\Controller\AbstractController;
use App\Controller\RequiresDmUserController;
use App\Controller\RequiresDmVersionController;
use App\Entity\Coa\InducksIssue;
use App\Entity\Dm\Achats;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\UsersPermissions;
use App\EntityTransform\FetchCollectionResult;
use App\EntityTransform\NumeroSimple;
use App\EntityTransform\UpdateCollectionResult;
use App\Helper\collectionUpdateHelper;
use App\Helper\PublicationHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController implements RequiresDmVersionController, RequiresDmUserController
{
    use collectionUpdateHelper;

    /**
     * @Route(methods={"GET"}, path="/collection/issues")
     */
    public function getIssues(): JsonResponse
    {
        /** @var EntityManager $dmEm */
        $dmEm = $this->container->get('doctrine')->getManager('dm');
        $issues = $dmEm->getRepository(Numeros::class)->findBy(
            ['idUtilisateur' => $this->getCurrentUser()['id']],
            ['pays' => 'asc', 'magazine' => 'asc', 'numero' => 'asc']
        );

        $result = new FetchCollectionResult();
        foreach ($issues as $issue) {
            $publicationCode = PublicationHelper::getPublicationCode($issue);
            $numero = $issue->getNumero();
            $etat = $issue->getEtat();

            if (!$result->getNumeros()->containsKey($publicationCode)) {
                $result->getNumeros()->set($publicationCode, new ArrayCollection());
            }

            $result->getNumeros()->get($publicationCode)->add(new NumeroSimple($numero, $etat));
        }

        $countryNames = json_decode(
            $this->callService(\App\Controller\Coa\AppController::class, 'listCountriesFromCodes', [
                'locale' => 'fr', // FIXME
                'countryCodes' => implode(',', array_unique(
                    array_map(function (Numeros $issue) {
                        return $issue->getPays();
                    }, $issues))
                )
            ])->getContent()
        );

        $publicationTitles = json_decode(
            $this->callService(\App\Controller\Coa\AppController::class, 'listPublicationsFromPublicationCodes', [
                'publicationCodes' => implode(',', array_unique(
                    array_map(function (Numeros $issue) {
                        return PublicationHelper::getPublicationCode($issue);
                    }, $issues)
                ))
            ])->getContent()
        );

        return new JsonResponse([
            'static' => [
                'pays' => $countryNames,
                'magazines' => $publicationTitles,
            ],
            'numeros' => $result->getNumeros()->toArray()
        ]);
    }

    /**
     * @Route(methods={"POST"}, path="/collection/issues")
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function postIssues(Request $request): JsonResponse
    {
        $country = $request->request->get('country');
        $publication = $request->request->get('publication');
        $issuenumbers = $request->request->get('issuenumbers');
        $condition = $request->request->get('condition');
        $istosell = $request->request->get('istosell');
        $purchaseid = $request->request->get('purchaseid');

        if ($condition === 'non_possede') {
            $nbRemoved = $this->deleteIssues($country, $publication, $issuenumbers);
            return new JsonResponse(
                self::getSimpleArray([new UpdateCollectionResult('DELETE', $nbRemoved)])
            );
        }

        [$nbUpdated, $nbCreated] = $this->addOrChangeIssues(
            $this->getEm('dm'),
            $this->getCurrentUser()['id'],
            $country,
            $publication,
            $issuenumbers,
            $condition,
            $istosell,
            $purchaseid
        );
        return new JsonResponse(self::getSimpleArray([
            new UpdateCollectionResult('UPDATE', $nbUpdated),
            new UpdateCollectionResult('CREATE', $nbCreated)
        ]));
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     path="/collection/purchases/{purchaseId}",
     *     defaults={"purchaseId"="NEW"})
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postPurchase(Request $request, ?string $purchaseId): ?Response
    {
        /** @var EntityManager $dmEm */
        $dmEm = $this->container->get('doctrine')->getManager('dm');

        $purchaseDate = $request->request->get('date');
        $purchaseDescription = $request->request->get('description');
        $idUser = $this->getCurrentUser()['id'];

        if ($purchaseId === 'NEW') {
            $purchase = new Achats();
        }
        else {
            $purchase = $dmEm->getRepository(Achats::class)->findOneBy(['idAcquisition' => $purchaseId, 'idUser' => $idUser]);
            if (is_null($purchase)) {
                return new Response('You don\'t have the rights to update this purchase', Response::HTTP_UNAUTHORIZED);
            }
        }

        $purchase->setIdUser($idUser);
        $purchase->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $purchaseDate.' 00:00:00'));
        $purchase->setDescription($purchaseDescription);

        $dmEm->persist($purchase);
        $dmEm->flush();

        return new Response();
    }

    /**
     * @Route(methods={"POST"}, path="/collection/bookcase/sort")
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setBookcaseSorting(Request $request): Response
    {
        $sorts = $request->request->get('sorts');

        if (is_array($sorts)) {
            /** @var EntityManager $dmEm */
            $dmEm = $this->container->get('doctrine')->getManager('dm');
            $qbMissingSorts = $dmEm->createQueryBuilder();
            $qbMissingSorts
                ->delete(BibliothequeOrdreMagazines::class, 'sorts')
                ->where('sorts.idUtilisateur = :userId')
                ->setParameter(':userId', $this->getCurrentUser()['id']);
            $qbMissingSorts->getQuery()->execute();

            $maxSort = -1;
            foreach($sorts as $publicationCode) {
                $sort = new BibliothequeOrdreMagazines();
                $sort->setPublicationcode($publicationCode);
                $sort->setOrdre(++$maxSort);
                $sort->setIdUtilisateur($this->getCurrentUser()['id']);
                $dmEm->persist($sort);
            }
            $dmEm->flush();
            return new JsonResponse(['max' => $maxSort]);
        }
        return new Response('Invalid sorts parameter',Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route(methods={"POST"}, path="/collection/inducks/import/init")
     */
    public function importFromInducksInit(Request $request): Response
    {
        $rawData = $request->request->get('rawData');

        if (strpos($rawData, 'country^entrycode^collectiontype^comment') === false) {
            return new Response('No headers', Response::HTTP_NO_CONTENT);
        }

        preg_match_all('#^((?!country)[^\n\^]+\^[^\n\^]+)\^[^\n\^]*\^.*$#m', $rawData, $matches, PREG_SET_ORDER);
        if (count($matches) === 0) {
            return new Response('No content', Response::HTTP_NO_CONTENT);
        }
        $matches = array_map(
            function($match) {
                return str_replace('^', '/', $match[1]);
            }, array_unique($matches, SORT_REGULAR)
        );

        /** @var EntityManager $coaEm */
        $coaEm = $this->container->get('doctrine')->getManager('coa');
        $coaIssuesQb = $coaEm->createQueryBuilder();

        $coaIssuesQb
            ->select('issues.issuecode', 'issues.publicationcode', 'issues.issuenumber')
            ->from(InducksIssue::class, 'issues')

            ->andWhere($coaIssuesQb->expr()->in('issues.issuecode',':issuesToImport'))
            ->setParameter(':issuesToImport', $matches);

        $issues = $coaIssuesQb->getQuery()->getArrayResult();

        $nonFoundIssues = array_values(array_diff($matches, array_map(function($issue) {
            return $issue['issuecode'];
        }, $issues)));

        $newIssues = $this->getNonPossessedIssues($issues, $this->getCurrentUser()['id']);

        return new JsonResponse([
            'issues' => $newIssues,
            'nonFoundIssues' => $nonFoundIssues,
            'existingIssuesCount' => count($issues) - count($newIssues)
        ]);
    }

    /**
     * @Route(methods={"POST"}, path="/collection/inducks/import")
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function importFromInducks(Request $request): Response
    {
        $issues = $request->request->get('issues');
        $defaultCondition = $request->request->get('defaultCondition');

        $newIssues = $this->getNonPossessedIssues($issues, $this->getCurrentUser()['id']);
        /** @var EntityManager $dmEm */
        $dmEm = $this->container->get('doctrine')->getManager('dm');

        foreach($newIssues as $issue) {
            [$country, $magazine] = explode('/', $issue['publicationcode']);
            $newIssue = new Numeros();
            $newIssue
                ->setIdUtilisateur($this->getCurrentUser()['id'])
                ->setPays($country)
                ->setMagazine($magazine)
                ->setNumero($issue['issuenumber'])
                ->setAv(false)
                ->setDateajout(new \DateTime())
                ->setEtat($defaultCondition);
            $dmEm->persist($newIssue);
        }
        $dmEm->flush();

        return new JsonResponse([
            'importedIssuesCount' => count($newIssues),
            'existingIssuesCount' => count($issues) - count($newIssues)
        ]);
    }

    /**
     * @Route(methods={"GET"}, path="/collection/privileges")
     */
    public function getUserPrivileges(): JsonResponse
    {
        $privileges = $this->getEm('dm')->getRepository(UsersPermissions::class)->findBy([
            'username' => $this->getCurrentUser()['username']
        ]);

        $privilegesAssoc = [];

        array_walk($privileges, function(UsersPermissions $value) use(&$privilegesAssoc) {
            $privilegesAssoc[$value->getRole()] = $value->getPrivilege();
        });

        return new JsonResponse($privilegesAssoc);
    }

    private function getNonPossessedIssues(array $issues, int $userId): array
    {
        /** @var EntityManager $dmEm */
        $dmEm = $this->container->get('doctrine')->getManager('dm');
        $currentIssues = $dmEm->getRepository(Numeros::class)->findBy(['idUtilisateur' => $userId]);

        $currentIssuesByPublication = [];
        foreach($currentIssues as $currentIssue) {
            $currentIssuesByPublication[$currentIssue->getPays().'/'.$currentIssue->getMagazine()][] = $currentIssue->getNumero();
        }

        return array_values(array_filter($issues, function($issue) use ($currentIssuesByPublication) {
            return (!(isset($currentIssuesByPublication[$issue['publicationcode']]) && in_array($issue['issuenumber'], $currentIssuesByPublication[$issue['publicationcode']], true)));
        }));
    }

    private function deleteIssues(string $country, string $publication, array $issueNumbers): int
    {
        /** @var EntityManager $dmEm */
        $dmEm = $this->container->get('doctrine')->getManager('dm');
        $qb = $dmEm->createQueryBuilder();
        $qb
            ->delete(Numeros::class, 'issues')

            ->andWhere($qb->expr()->eq('issues.pays', ':country'))
            ->setParameter(':country', $country)

            ->andWhere($qb->expr()->eq('issues.magazine', ':publication'))
            ->setParameter(':publication', $publication)

            ->andWhere($qb->expr()->in('issues.numero', ':issuenumbers'))
            ->setParameter(':issuenumbers', $issueNumbers)

            ->andWhere($qb->expr()->in('issues.idUtilisateur', ':userId'))
            ->setParameter(':userId', $this->getCurrentUser()['id']);

        return $qb->getQuery()->getResult();
    }
}
