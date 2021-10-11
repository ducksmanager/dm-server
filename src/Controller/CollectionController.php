<?php

namespace App\Controller;

use App\Entity\Coa\InducksIssue;
use App\Entity\Dm\Abonnements;
use App\Entity\Dm\Achats;
use App\Entity\Dm\AuteursPseudos;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersOptions;
use App\Entity\Dm\UsersPermissions;
use App\EntityTransform\UpdateCollectionResult;
use App\Helper\Email\FeedbackSent;
use App\Helper\JsonResponseFromObject;
use App\Service\BookcaseService;
use App\Service\CollectionUpdateService;
use App\Service\ContributionService;
use App\Service\EmailService;
use App\Service\UsersOptionsService;
use DateInterval;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use Psr\Log\LoggerInterface;
use Pusher\PushNotifications\PushNotifications;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CollectionController extends AbstractController implements RequiresDmVersionController, InjectsDmUserController
{
    /**
     * @Route(methods={"GET"}, path="collection/notification_token")
     */
    public function getNotificationToken(Request $request): Response
    {
        $currentUsername = $this->getSessionUser()['username'];
        $passedUsername = $request->query->get('user_id');

        if ($currentUsername !== $passedUsername) {
            return new Response(Response::HTTP_UNAUTHORIZED);
        }

        try {
            $beamsClient = new PushNotifications([
                'instanceId' => $_ENV['PUSHER_INSTANCE_ID'],
                'secretKey' => $_ENV['PUSHER_SECRET_KEY'],
            ]);
            return new JsonResponse($beamsClient->generateToken($currentUsername));
        } catch (Exception $e) {
            return new Response(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route(methods={"GET"}, path="collection/notifications/countries")
     */
    public function getCountriesToNotify(UsersOptionsService $usersOptionsService): Response
    {
        $currentUser = $this->getEm('dm')->getRepository(Users::class)->find($this->getSessionUser()['id']);

        return new JsonResponseFromObject($usersOptionsService->getOptionValueForUser(
            $currentUser,
            UsersOptionsService::OPTION_NAME_SUGGESTION_NOTIFICATION_COUNTRY
        )->getValue()
        );
    }

    /**
     * @Route(methods={"POST"}, path="collection/notifications/countries")
     */
    public function updateCountriesToNotify(Request $request): Response
    {
        $countries = $request->request->get('countries');

        /** @var Users $currentUser */
        $dmEm = $this->getEm('dm');
        $currentUser = $dmEm->getRepository(Users::class)->find($this->getSessionUser()['id']);
        $optionName = 'suggestion_notification_country';

        try {
            $qbDeleteExistingValues = ($dmEm->createQueryBuilder())
                ->delete(UsersOptions::class, 'options')
                ->where('options.user = :user AND options.optionNom = :optionName')
                ->setParameter(':user', $currentUser)
                ->setParameter(':optionName', $optionName);

            $qbDeleteExistingValues->getQuery()->execute();

            foreach ($countries as $countryCode) {
                $currentUser->getOptions()->add(
                    (new UsersOptions())
                        ->setUser($currentUser)
                        ->setOptionNom($optionName)
                        ->setOptionValeur($countryCode)
                );
            }
            $dmEm->flush();
        } catch (ORMException $e) {
            return new Response('Error when updating user options', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('OK', Response::HTTP_OK);
    }

    /**
     * @Route(methods={"POST"}, path="collection/lastvisit")
     */
    public function updateLastVisit(LoggerInterface $logger): JsonResponseFromObject
    {
        $dmEm = $this->getEm('dm');
        /** @var Users $existingUser */
        $existingUser = $dmEm->getRepository(Users::class)->find($this->getSessionUser()['id']);

        if (is_null($existingUser->getDernieracces())) {
            $logger->info("Initializing last access for user {$existingUser->getId()}");
        } else if ($existingUser->getDernieracces()->format('Y-m-d') < (new DateTime())->format('Y-m-d')) {
            $logger->info("Updating last access for user {$existingUser->getId()}");
            $existingUser->setPrecedentacces($existingUser->getDernieracces());
        } else {
            return new JsonResponseFromObject([
                'previousVisit' => ($existingUser->getPrecedentacces() ?? new DateTime())->format('Y-m-d')
            ], Response::HTTP_ACCEPTED);
        }

        $existingUser->setDernieracces(new DateTime());
        $dmEm->persist($existingUser);
        $dmEm->flush();

        return new JsonResponseFromObject([
            'previousVisit' => ($existingUser->getPrecedentacces() ?? new DateTime())->format('Y-m-d')
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * @Route(methods={"GET"}, path="/collection/user")
     */
    public function getDmUser()
    {
        $existingUser = $this->getEm('dm')->getRepository(Users::class)->find($this->getSessionUser()['id']);
        return new JsonResponseFromObject($existingUser);
    }

    /**
     * @Route(methods={"GET"}, path="/collection/issues")
     */
    public function getIssues(): JsonResponse
    {
        $dmEm = $this->getEm('dm');
        $qb = $dmEm->createQueryBuilder();
        $qb->select('issues.id, issues.pays AS country, issues.magazine, issues.numero AS issueNumber, issues.etat AS condition, issues.idAcquisition AS purchaseId, issues.dateajout AS creationDate')
            ->from(Numeros::class, 'issues')
            ->where($qb->expr()->eq('issues.idUtilisateur', $this->getSessionUser()['id']))
            ->orderBy('issues.pays, issues.magazine, issues.numero', 'ASC');

        return new JsonResponseFromObject(
            array_map(function ($result) {
                $result['creationDate'] = $result['creationDate']->format('Y-m-d');
                return $result;
            }, $qb->getQuery()->getArrayResult()
            ));
    }

    /**
     * @Route(methods={"GET"}, path="/collection/purchases")
     */
    public function getPurchases(): JsonResponse
    {
        $dmEm = $this->getEm('dm');
        $qb = $dmEm->createQueryBuilder();
        $qb->select('purchases.idAcquisition AS id, purchases.description, concat(purchases.date, \'\') AS date')
            ->from(Achats::class, 'purchases')
            ->where($qb->expr()->eq('purchases.idUser', $this->getSessionUser()['id']))
            ->orderBy('purchases.date', 'ASC');

        return new JsonResponse($qb->getQuery()->getArrayResult());
    }

    /**
     * @Route(methods={"POST"}, path="/collection/authors/watched")
     */
    public function updateWatchedAuthor(Request $request): Response
    {
        $personCode = $request->request->get('personCode');
        $notation = $request->request->get('notation');

        $qb = $this->getEm('dm')->createQueryBuilder();
        $qb->update(AuteursPseudos::class, 'auteurs_pseudos')
            ->set('auteurs_pseudos.notation', ':notation')
            ->setParameter('notation', $notation)
            ->andWhere($qb->expr()->eq('auteurs_pseudos.idUser', ':idUser'))
            ->setParameter('idUser', $this->getSessionUser()['id'])
            ->andWhere($qb->expr()->eq('auteurs_pseudos.nomauteurabrege', ':personCode'))
            ->setParameter('personCode', $personCode);
        $qb->getQuery()->execute();

        return new Response();
    }

    /**
     * @Route(methods={"PUT"}, path="/collection/authors/watched")
     */
    public function createWatchedAuthor(Request $request): Response
    {
        $existingWatchedAuthors = $this->getEm('dm')->getRepository(AuteursPseudos::class)->findBy([
            'idUser' => $this->getSessionUser()['id']
        ]);
        if (count($existingWatchedAuthors) >= 5) {
            return new Response('At most 5 authors can be watched per user', Response::HTTP_FORBIDDEN);
        }
        $personCode = $request->request->get('personCode');

        $author = (new AuteursPseudos())
            ->setIdUser($this->getSessionUser()['id'])
            ->setNomauteurabrege($personCode)
            ->setNotation(5);
        $this->getEm('dm')->persist($author);
        $this->getEm('dm')->flush();

        return new Response();
    }

    /**
     * @Route(methods={"DELETE"}, path="/collection/authors/watched")
     */
    public function deleteWatchedAuthor(Request $request): Response
    {
        $personCode = $request->request->get('personCode');

        $qb = $this->getEm('dm')->createQueryBuilder();
        $qb->delete(AuteursPseudos::class, 'auteurs_pseudos')
            ->andWhere($qb->expr()->eq('auteurs_pseudos.idUser', ':idUser'))
            ->setParameter('idUser', $this->getSessionUser()['id'])
            ->andWhere($qb->expr()->eq('auteurs_pseudos.nomauteurabrege', ':personCode'))
            ->setParameter('personCode', $personCode);

        $qb->getQuery()->execute();

        return new Response();
    }

    /**
     * @Route(methods={"GET"}, path="/collection/authors/watched")
     */
    public function getWatchedAuthors(): JsonResponse
    {
        return new JsonResponse(
            array_map(fn(AuteursPseudos $result) => [
                'personCode' => $result->getNomauteurabrege(),
                'notation' => $result->getNotation()
            ], $this->getEm('dm')->getRepository(AuteursPseudos::class)->findBy([
                'idUser' => $this->getSessionUser()['id']
            ])));
    }

    /**
     * @Route(methods={"GET"}, path="/collection/popular")
     */
    public function getPopularIssuesInCollection(BookcaseService $bookcaseService): JsonResponse
    {
        return new JsonResponse($bookcaseService->getCollectionPopularIssues($this->getSessionUser()['id']));
    }

    /**
     * @Route(methods={"POST"}, path="/collection/issues")
     * @return Response
     * @throws Exception
     */
    public function postIssues(Request $request, LoggerInterface $logger, CollectionUpdateService $collectionUpdateService): Response
    {
        $publication = $request->request->get('publicationCode');
        $issueNumbers = $request->request->get('issueNumbers');
        $condition = $request->request->get('condition');
        $userId = $this->getSessionUser()['id'];

        if (is_array($condition) && count($issueNumbers) > 1) {
            return new Response("Can't update copies of multiple issues at once", Response::HTTP_BAD_REQUEST);
        }

        $isToSell = $request->request->get('istosell');
        $purchaseId = $request->request->get('purchaseId');

        $purchaseIds = is_array($purchaseId) ? $purchaseId : [$purchaseId];
        $this->checkPurchaseIdsBelongToUser($logger, $purchaseIds);

        if (is_array($condition)) {
            [$nbUpdated, $nbCreated] = $collectionUpdateService->addOrChangeCopies(
                $userId, $publication, $issueNumbers[0], $condition ?? [], $isToSell ?? [], $purchaseIds ?? []
            );
        }
        else {
            if (in_array($condition, ['non_possede', 'missing'])) {
                $collectionUpdateService->deleteIssues($userId, $publication, $issueNumbers);
                return new JsonResponse(
                    self::getSimpleArray([])
                );
            }
            [$nbUpdated, $nbCreated] = $collectionUpdateService->addOrChangeIssues(
                $userId, $publication, $issueNumbers, $condition, $isToSell, $purchaseIds[0]
            );
        }
        return new JsonResponse(self::getSimpleArray([
            new UpdateCollectionResult('UPDATE', $nbUpdated),
            new UpdateCollectionResult('CREATE', $nbCreated)
        ]));
    }

    private function checkPurchaseIdsBelongToUser(LoggerInterface $logger, &$purchaseIds) {
        foreach($purchaseIds as &$purchaseId) {
            if ($purchaseId === 'do_not_change') {
                $purchaseId = -1;
            } else if (!$this->getUserPurchase($purchaseId)) {
                $logger->warning("User {$this->getSessionUser()['id']} tried to use purchase ID $purchaseId which is owned by another user");
                $purchaseId = null;
            }
        }
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     path="/collection/purchases/{purchaseId}",
     *     defaults={"purchaseId"="NEW"})
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postPurchase(Request $request, TranslatorInterface $translator, ?string $purchaseId): ?Response
    {
        $dmEm = $this->getEm('dm');

        $purchaseDateStr = $request->request->get('date');
        $purchaseDate = DateTime::createFromFormat('Y-m-d H:i:s', $purchaseDateStr . ' 00:00:00');
        $purchaseDescription = $request->request->get('description');
        $idUser = $this->getSessionUser()['id'];

        if ($purchaseId === 'NEW') {
            $duplicatePurchase = $dmEm->getRepository(Achats::class)->findOneBy([
                'idUser' => $this->getSessionUser()['id'],
                'date' => $purchaseDate,
                'description' => $purchaseDescription
            ]);
            if (!is_null($duplicatePurchase)) {
                return new Response($translator->trans('ERROR_PURCHASE_ALREADY_EXISTS'), Response::HTTP_CONFLICT);
            }
            $purchase = new Achats();
        } else {
            $purchase = $this->getUserPurchase($purchaseId);
            if (is_null($purchase)) {
                return new Response($translator->trans('ERROR_PURCHASE_UPDATE_NOT_ALLOWED'), Response::HTTP_UNAUTHORIZED);
            }
        }

        $purchase->setIdUser($idUser);
        $purchase->setDate($purchaseDate);
        $purchase->setDescription($purchaseDescription);

        $dmEm->persist($purchase);
        $dmEm->flush();

        return new Response();
    }

    /**
     * @Route(
     *     methods={"DELETE"},
     *     path="/collection/purchases/{purchaseId}"
     * )
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deletePurchase(TranslatorInterface $translator, ?string $purchaseId): ?Response
    {
        $dmEm = $this->getEm('dm');
        $idUser = $this->getSessionUser()['id'];

        $purchase = $this->getUserPurchase($purchaseId);
        if (is_null($purchase)) {
            return new Response($translator->trans('ERROR_PURCHASE_DELETE_NOT_ALLOWED'), Response::HTTP_UNAUTHORIZED);
        }

        $qb = $this->getEm('dm')->createQueryBuilder();
        $qb->update(Numeros::class, 'issues')
            ->set('issues.idAcquisition', '-1')
            ->andWhere($qb->expr()->eq('issues.idUtilisateur', ':idUser'))
            ->setParameter('idUser', $this->getSessionUser()['id'])
            ->andWhere($qb->expr()->eq('issues.idAcquisition', ':purchaseId'))
            ->setParameter('purchaseId', $purchaseId);
        $qb->getQuery()->execute();

        $dmEm->remove($purchase);
        $dmEm->flush();

        return new Response();
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

        preg_match_all('#^((?!country)[^\n^]+\^[^\n^]+)\^[^\n^]*\^.*$#m', $rawData, $matches, PREG_SET_ORDER);
        if (count($matches) === 0) {
            return new Response('No content', Response::HTTP_NO_CONTENT);
        }
        $matches = array_map(
            fn($match) => str_replace('^', '/', $match[1]), array_unique($matches, SORT_REGULAR)
        );

        $coaEm = $this->getEm('coa');
        $coaIssuesQb = $coaEm->createQueryBuilder();

        $coaIssuesQb
            ->select('issues.issuecode', 'issues.publicationcode', 'issues.issuenumber')
            ->from(InducksIssue::class, 'issues')
            ->andWhere($coaIssuesQb->expr()->in('issues.issuecode', ':issuesToImport'))
            ->setParameter(':issuesToImport', $matches);

        $issues = $coaIssuesQb->getQuery()->getArrayResult();

        $nonFoundIssues = array_values(array_diff($matches, array_map(fn($issue) => $issue['issuecode'], $issues)));

        $newIssues = $this->getNonPossessedIssues($issues, $this->getSessionUser()['id']);

        return new JsonResponse([
            'issues' => $newIssues,
            'nonFoundIssues' => $nonFoundIssues,
            'existingIssuesCount' => count($issues) - count($newIssues)
        ]);
    }

    /**
     * @Route(methods={"POST"}, path="/collection/inducks/import")
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function importFromInducks(Request $request): Response
    {
        $issues = $request->request->get('issues');
        $defaultCondition = $request->request->get('defaultCondition');

        $newIssues = $this->getNonPossessedIssues($issues, $this->getSessionUser()['id']);
        $dmEm = $this->getEm('dm');

        foreach ($newIssues as $issue) {
            [$country, $magazine] = explode('/', $issue['publicationcode']);
            $newIssue = new Numeros();
            $newIssue
                ->setIdUtilisateur($this->getSessionUser()['id'])
                ->setPays($country)
                ->setMagazine($magazine)
                ->setNumero($issue['issuenumber'])
                ->setAv(false)
                ->setDateajout(new DateTime())
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
            'username' => $this->getSessionUser()['username']
        ]);

        $privilegesAssoc = [];

        array_walk($privileges, function (UsersPermissions $value) use (&$privilegesAssoc) {
            $privilegesAssoc[$value->getRole()] = $value->getPrivilege();
        });

        return new JsonResponse($privilegesAssoc);
    }

    /**
     * @Route(methods={"GET"}, path="/collection/subscriptions")
     */
    public function getUserSubscriptions(): JsonResponse
    {
        $subscriptions = $this->getEm('dm')->getRepository(Abonnements::class)->findBy([
            'user' => $this->getSessionUser()['id']
        ]);

        return new JsonResponseFromObject(array_map(fn(Abonnements $subscription) => [
            'id' => $subscription->getId(),
            'publicationCode' => $subscription->getPays() . '/' . $subscription->getMagazine(),
            'startDate' => $subscription->getDateDebut()->format('Y-m-d'),
            'endDate' => $subscription->getDateFin()->format('Y-m-d')
        ], $subscriptions));
    }

    /**
     * @Route(
     *     methods={"POST", "PUT"},
     *     path="/collection/subscriptions/{subscriptionId}",
     *     defaults={"subscriptionId"=null}
     * )
     */
    public function createOrEditUserSubscription(Request $request, ?int $subscriptionId = null): Response
    {
        $user = $this->getEm('dm')->getRepository(Users::class)->find($this->getSessionUser()['id']);
        if (is_null($subscriptionId)) {
            [$country, $magazine] = explode('/', $request->request->get('publicationCode'));
            $subscription = (new Abonnements())
                ->setPays($country)
                ->setMagazine($magazine)
                ->setUser($user);
        }
        else {
            /** @var Abonnements $subscription */
            $subscription = $this->getEm('dm')->getRepository(Abonnements::class)->find($subscriptionId);
            if ($subscription->getUser() !== $user) {
                return new Response('This subscription doesn\'t belong to you', Response::HTTP_FORBIDDEN);
            }
        }
        $subscription
            ->setDateDebut(new DateTime($request->request->get('startDate')))
            ->setDateFin(new DateTime($request->request->get('endDate')));

        $this->getEm('dm')->persist($subscription);
        $this->getEm('dm')->flush();

        if (is_null($subscriptionId)) {
            return new Response('OK', Response::HTTP_CREATED);
        }

        return new Response('OK', Response::HTTP_OK);
    }

    /**
     * @Route(methods={"DELETE"}, path="/collection/subscriptions/{subscriptionId}")
     */
    public function deleteUserSubscription(int $subscriptionId): Response
    {
        $qb = ($this->getEm('dm')->createQueryBuilder())
            ->delete(Abonnements::class, 'subscriptions')
            ->andWhere('subscriptions.id = :subscriptionId')
            ->setParameter('subscriptionId', $subscriptionId)
            ->andWhere('subscriptions.user = :user')
            ->setParameter('user', $this->getEm('dm')->getRepository(Users::class)->find($this->getSessionUser()['id']));

        $qb->getQuery()->execute();

        return new Response('OK', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(methods={"GET"}, path="/collection/edges/lastPublished")
     */
    public function getLastPublishedEdges(): Response
    {
        $qb = ($this->getEm('dm')->createQueryBuilder());
        $qb
            ->select('edges')
            ->from(TranchesPretes::class, 'edges')
            ->innerJoin(Numeros::class, 'issues', Join::WITH, $qb->expr()->eq(
                'edges.issuecode','issues.issuecode'
            ))
            ->andWhere('issues.idUtilisateur = :userId')
            ->setParameter('userId', $this->getSessionUser()['id'])
            ->andWhere('edges.dateajout > :threeMonthsAgo')
            ->setParameter('threeMonthsAgo', (new DateTime())->sub(new DateInterval('P3M')))
            ->setMaxResults(5);
        $results = $qb->getQuery()->getResult();

        return new JsonResponseFromObject(array_map(fn(TranchesPretes $edge) => [
            'id' => $edge->getId(),
            'publicationcode' => $edge->getPublicationcode(),
            'issuenumber' => $edge->getIssuenumber(),
            'creationDate' => $edge->getDateajout()->format('Y-m-d'),
        ], $results));
    }

    /**
     * @Route(methods={"GET"}, path="/collection/points")
     */
    public function getMedalPoints(ContributionService $contributionService) {
        $medalStats = $contributionService->getMedalPoints([$this->getSessionUser()['id']]);

        return new JsonResponse($medalStats);
    }

    /**
     * @Route(methods={"POST"}, path="/collection/feedback")
     */
    public function sendFeedback(Request $request, EmailService $emailService, TranslatorInterface $translator): Response
    {
        $emailService->send(new FeedbackSent(
            $translator,
            $this->getEm('dm')->getRepository(Users::class)->find($this->getSessionUser()['id']),
            $request->request->get('message')
        ));

        return new Response();
    }

    private function getNonPossessedIssues(array $issues, int $userId): array
    {
        $dmEm = $this->getEm('dm');
        $currentIssues = $dmEm->getRepository(Numeros::class)->findBy(['idUtilisateur' => $userId]);

        $currentIssuesByPublication = [];
        foreach ($currentIssues as $currentIssue) {
            $currentIssuesByPublication[$currentIssue->getPays() . '/' . $currentIssue->getMagazine()][] = $currentIssue->getNumero();
        }

        return array_values(array_filter($issues, fn($issue) => (!(isset($currentIssuesByPublication[$issue['publicationcode']]) && in_array($issue['issuenumber'], $currentIssuesByPublication[$issue['publicationcode']], true)))));
    }

    private function getUserPurchase(?int $purchaseId): ?Achats
    {
        return is_null($purchaseId)
            ? null
            : $this->getEm('dm')->getRepository(Achats::class)->findOneBy([
                'idAcquisition' => $purchaseId,
                'idUser' => $this->getSessionUser()['id']
            ]);
    }
}
