<?php

namespace App\Controller;

use App\Entity\Coa\InducksIssue;
use App\Entity\Dm\Abonnements;
use App\Entity\Dm\Achats;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersOptions;
use App\Entity\Dm\UsersPermissions;
use App\EntityTransform\UpdateCollectionResult;
use App\Helper\Email\FeedbackSentEmail;
use App\Helper\JsonResponseFromObject;
use App\Service\CollectionUpdateService;
use App\Service\EmailService;
use App\Service\UsersOptionsService;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Log\LoggerInterface;
use Pusher\PushNotifications\PushNotifications;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CollectionController extends AbstractController implements RequiresDmVersionController, RequiresDmUserController
{
    /**
     * @Route(methods={"GET"}, path="collection/notification_token")
     */
    public function getNotificationToken(Request $request) : Response {
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
    public function getCountriesToNotify(UsersOptionsService $usersOptionsService) : Response {
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
    public function updateCountriesToNotify(Request $request) : Response {
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

            foreach($countries as $countryCode) {
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
    public function updateLastVisit(LoggerInterface $logger) : Response {
        $dmEm = $this->getEm('dm');
        $existingUser = $dmEm->getRepository(Users::class)->find($this->getSessionUser()['id']);

        if (is_null($existingUser->getDernieracces()) ) {
            $logger->info("Initializing last access for user {$existingUser->getId()}");
        }
        else if ($existingUser->getDernieracces()->format('Y-m-d') < (new DateTime())->format('Y-m-d')) {
            $logger->info("Updating last access for user {$existingUser->getId()}");
            $existingUser->setPrecedentacces($existingUser->getDernieracces());
        }
        else {
            return new Response('OK', Response::HTTP_NO_CONTENT);
        }

        $existingUser->setDernieracces(new DateTime());
        $dmEm->persist($existingUser);
        $dmEm->flush();

        return new Response('OK', Response::HTTP_ACCEPTED);
    }

    /**
     * @Route(methods={"GET"}, path="/collection/user")
     */
    public function getDmUser() {
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
     * @Route(methods={"POST"}, path="/collection/issues")
     * @return JsonResponse
     * @throws Exception
     */
    public function postIssues(Request $request, LoggerInterface $logger, CollectionUpdateService $collectionUpdateService): JsonResponse
    {
        $publication = $request->request->get('publicationCode');
        $issueNumbers = $request->request->get('issueNumbers');
        $condition = $request->request->get('condition');

        if ($condition === 'non_possede') {
            $nbRemoved = $this->deleteIssues($publication, $issueNumbers);
            return new JsonResponse(
                self::getSimpleArray([new UpdateCollectionResult('DELETE', $nbRemoved)])
            );
        }

        $isToSell = $request->request->get('istosell');
        $purchaseId = $request->request->get('purchaseId');

        if (!$this->getUserPurchase($purchaseId)) {
            $logger->warning("User {$this->getSessionUser()['id']} tried to use purchase ID $purchaseId which is owned by another user");
            $purchaseId = null;
        }
        [$nbUpdated, $nbCreated] = $collectionUpdateService->addOrChangeIssues(
            $this->getSessionUser()['id'], $publication, $issueNumbers, $condition, $isToSell, $purchaseId
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postPurchase(Request $request, TranslatorInterface $translator, ?string $purchaseId): ?Response
    {
        $dmEm = $this->getEm('dm');

        $purchaseDateStr = $request->request->get('date');
        $purchaseDate = DateTime::createFromFormat('Y-m-d H:i:s', $purchaseDateStr.' 00:00:00');
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
        }
        else {
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
     * @Route(methods={"POST"}, path="/collection/bookcase/sort")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setBookcaseSorting(Request $request): Response
    {
        $sorts = $request->request->get('sorts');

        if (is_array($sorts)) {
            $dmEm = $this->getEm('dm');
            $qbMissingSorts = $dmEm->createQueryBuilder();
            $qbMissingSorts
                ->delete(BibliothequeOrdreMagazines::class, 'sorts')
                ->where('sorts.idUtilisateur = :userId')
                ->setParameter(':userId', $this->getSessionUser()['id']);
            $qbMissingSorts->getQuery()->execute();

            $maxSort = -1;
            foreach($sorts as $publicationCode) {
                $sort = new BibliothequeOrdreMagazines();
                $sort->setPublicationcode($publicationCode);
                $sort->setOrdre(++$maxSort);
                $sort->setIdUtilisateur($this->getSessionUser()['id']);
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

        preg_match_all('#^((?!country)[^\n^]+\^[^\n^]+)\^[^\n^]*\^.*$#m', $rawData, $matches, PREG_SET_ORDER);
        if (count($matches) === 0) {
            return new Response('No content', Response::HTTP_NO_CONTENT);
        }
        $matches = array_map(
            function($match) {
                return str_replace('^', '/', $match[1]);
            }, array_unique($matches, SORT_REGULAR)
        );

        $coaEm = $this->getEm('coa');
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

        foreach($newIssues as $issue) {
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

        array_walk($privileges, function(UsersPermissions $value) use(&$privilegesAssoc) {
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
            'idUtilisateur' => $this->getSessionUser()['id']
        ]);

        return new JsonResponseFromObject(array_map(function(Abonnements $subscription) {
            return [
                'id' => $subscription->getId(),
                'publicationCode' => $subscription->getPays().'/'.$subscription->getMagazine(),
                'startDate' => $subscription->getDateDebut()->format('Y-m-d'),
                'endDate' => $subscription->getDateFin()->format('Y-m-d')
            ];
        }, $subscriptions));
    }

    /**
     * @Route(methods={"PUT"}, path="/collection/subscriptions")
     */
    public function createUserSubscription(Request $request, ValidatorInterface $validator, LoggerInterface $logger): Response
    {
        $validationResult = $validator->validate($request->request->all(), new Collection(array(
            'publicationCode' => new Regex(['pattern' => '#^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$#']),
            'startDate'  => new Date(),
            'endDate'  => new Date(),
        )));

        if ($validationResult->count() > 0) {
            return new JsonResponse($validationResult, Response::HTTP_BAD_REQUEST);
        }

        [$country, $magazine] = explode('/', $request->request->get('publicationCode'));
        $subscription = (new Abonnements())
            ->setIdUtilisateur($this->getEm('dm')->getRepository(Users::class)->find($this->getSessionUser()['id']))
            ->setDateDebut(new DateTime($request->request->get('startDate')))
            ->setDateFin(new DateTime($request->request->get('endDate')))
            ->setPays($country)
            ->setMagazine($magazine);

        $this->getEm('dm')->persist($subscription);
        $this->getEm('dm')->flush();

        return new Response('Created', Response::HTTP_CREATED);
    }

    /**
     * @Route(methods={"POST"}, path="/collection/feedback")
     */
    public function sendFeedback(Request $request, EmailService $emailService, TranslatorInterface $translator): Response
    {
        $emailService->send(new FeedbackSentEmail(
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
        foreach($currentIssues as $currentIssue) {
            $currentIssuesByPublication[$currentIssue->getPays().'/'.$currentIssue->getMagazine()][] = $currentIssue->getNumero();
        }

        return array_values(array_filter($issues, function($issue) use ($currentIssuesByPublication) {
            return (!(isset($currentIssuesByPublication[$issue['publicationcode']]) && in_array($issue['issuenumber'], $currentIssuesByPublication[$issue['publicationcode']], true)));
        }));
    }

    private function deleteIssues(string $publicationCode, array $issueNumbers): int
    {
        $dmEm = $this->getEm('dm');
        $qb = $dmEm->createQueryBuilder();
        $qb
            ->delete(Numeros::class, 'issues')

            ->andWhere($qb->expr()->eq($qb->expr()->concat('issues.pays',  $qb->expr()->literal('/'), 'issues.magazine'), ':publicationCode'))
            ->setParameter(':publicationCode', $publicationCode)

            ->andWhere($qb->expr()->in('issues.numero', ':issueNumbers'))
            ->setParameter(':issueNumbers', $issueNumbers)

            ->andWhere($qb->expr()->in('issues.idUtilisateur', ':userId'))
            ->setParameter(':userId', $this->getSessionUser()['id']);

        return $qb->getQuery()->getResult();
    }

    private function getUserPurchase(?int $purchaseId) : ?Achats {
        return is_null($purchaseId)
            ? null
            : $this->getEm('dm')->getRepository(Achats::class)->findOneBy([
                'idAcquisition' => $purchaseId,
                'idUser' => $this->getSessionUser()['id']
            ]);
    }
}
