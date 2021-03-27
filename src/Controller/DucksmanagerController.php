<?php
namespace App\Controller;

use App\Entity\Coa\InducksPublication;
use App\Entity\Dm\Achats;
use App\Entity\Dm\AuteursPseudos;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Bouquineries;
use App\Entity\Dm\Demo;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersContributions;
use App\Entity\Dm\UsersOptions;
use App\Entity\Dm\UsersPasswordTokens;
use App\Helper\Email\AbstractEmail;
use App\Helper\Email\BookstoreApproved;
use App\Helper\Email\BookstoreSuggested;
use App\Helper\Email\EdgesPublished;
use App\Helper\Email\ResetPassword;
use App\Helper\Email\SubscriptionIssueAdded;
use App\Helper\JsonResponseFromObject;
use App\Service\CollectionUpdateService;
use App\Service\ContributionService;
use App\Service\CsvService;
use App\Service\EmailService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DucksmanagerController extends AbstractController
{
    /**
     * @Route(methods={"PUT"}, path="/ducksmanager/user")
     */
    public function createUser(Request $request, TranslatorInterface $translator): Response {
        $userCheckError = $this->checkNewUser(
            $translator,
            $request->request->get('username'),
            $request->request->get('password'),
            $request->request->get('password2')
        );

        if (isset($userCheckError)) {
            return new Response($userCheckError, Response::HTTP_PRECONDITION_FAILED);
        }

        try {
            if ($this->createUserNoCheck(
                $request->request->get('username'),
                $request->request->get('password'),
                $request->request->get('email')
            )) {
                return new Response('OK', Response::HTTP_CREATED);
            }
        } catch (OptimisticLockException|ORMException $e) {
            return new Response('KO', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('KO', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/resetpassword/checktoken/{token}")
     */
    public function checkPasswordToken(string $token) {
        $existingToken = $this->getEm('dm')->getRepository(UsersPasswordTokens::class)->findOneBy([
            'token' => $token
        ]);
        if (!is_null($existingToken)) {
            return new JsonResponse(['token' => $token, 'userId' => $existingToken->getIdUser()]);
        }
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/resetpassword/init")
     * @throws ORMException
     * @throws Exception
     */
    public function resetPasswordInit(Request $request, EmailService $emailService, LoggerInterface $logger, TranslatorInterface $translator): Response
    {
        $email = $request->request->get('email');
        $dmEm = $this->getEm('dm');

        $user = $dmEm->getRepository(Users::class)->findOneBy([
            'email' => $email
        ]);

        if (is_null($user)) {
            $logger->info('A visitor requested to reset a password for an invalid e-mail : ' . $email);
            return new Response('OK');
        }

        $logger->info('A visitor requested to reset a password for a valid e-mail : ' . $email);
        $token = bin2hex(random_bytes(8));
        $passwordToken = new UsersPasswordTokens();
        $passwordToken->setIdUser($user->getId());
        $passwordToken->setToken($token);
        $dmEm->persist($passwordToken);
        $dmEm->flush();

        try {
            $emailService->send(new ResetPassword($translator, $user, $token));
        }
        catch(Exception $e) {
            return new Response('KO', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new Response();
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/resetpassword")
     * @throws ORMException
     */
    public function resetPassword(Request $request) {
        $token = $request->request->get('token');
        $password = $request->request->get('password');

        $dmEm = $this->getEm('dm');

        /** @var UsersPasswordTokens $existingToken */
        $existingToken = $dmEm->getRepository(UsersPasswordTokens::class)->findOneBy([
            'token' => $token
        ]);
        if (!is_null($existingToken)) {
            $user = $dmEm->getRepository(Users::class)->findOneBy([
                'id' => $existingToken->getIdUser()
            ]);
            $user->setPassword(sha1($password));
            $dmEm->persist($user);
            $dmEm->remove($existingToken);
            $dmEm->flush();

            return new JsonResponse(['userId' => $user->getId()]);
        }
        return new Response('', Response::HTTP_NO_CONTENT);

    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/resetDemo")
     * @return JsonResponseFromObject|Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function resetDemo(CollectionUpdateService $collectionUpdateService, CsvService $csvService) {
        $dmEm = $this->getEm('dm');
        /** @var Demo $demoData */
        $demoData = $dmEm->getRepository(Demo::class)->find(1);
        $lastDemoInit = $demoData->getDatedernierinit();
        if (!(
            (int) $lastDemoInit->format('%H') < (int) (new DateTime())->format('%H')
            || $lastDemoInit->getTimestamp() + 3600 < (new DateTime())->getTimestamp())
        ) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $demoData->setDatedernierinit(new DateTime());
        $dmEm->persist($demoData);

        /** @var Users $demoUser */
        $demoUser = $dmEm->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        if (!is_null($demoUser)) {
            if (!$this->deleteUserData($demoUser)) {
                return new Response('Error while removing demo user data', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            try {
                if (!$this->resetBookcaseOptions($demoUser)) {
                    return new Response('Error while resetting bookcase options', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } catch (OptimisticLockException|ORMException $e) {
                return new Response('Error while resetting bookcase options : '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $demoUserIssueData = $csvService->readCsv('demo_user/issues.csv');

            foreach ($demoUserIssueData as $publicationData) {
                $collectionUpdateService->addOrChangeIssues(
                    $demoUser->getId(), $publicationData['publicationCode'], $publicationData['issueNumbers'], $publicationData['condition'], null, null
                );
            }

            $demoUserPurchaseData = $csvService->readCsv('demo_user/purchases.csv');

            foreach ($demoUserPurchaseData as $purchaseData) {
                $purchase = new Achats();
                $purchase->setDate(DateTime::createFromFormat('Y-m-d H:i:s', $purchaseData['date'].' 00:00:00'));
                $purchase->setDescription($purchaseData['description']);
                $purchase->setIdUser($demoUser->getId());

                $dmEm->persist($purchase);
            }

            $dmEm->flush();
        }
        else {
            return new Response('Malformed demo user data or no data user', Response::HTTP_EXPECTATION_FAILED);
        }

        return new JsonResponseFromObject($demoUser);
    }

    /**
     * @Route(methods={"GET"}, path="/ducksmanager/bookcase/{userId}/sort/max")
     */
    public function getLastPublicationPosition(int $userId) : JsonResponse {
        $dmEm = $this->getEm('dm');
        $qb = $dmEm->createQueryBuilder();
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
            return new JsonResponse(['max' => (int) $maxSort], is_null($maxSort) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/emails/subscription/release")
     * @return Response
     * @throws QueryException
     */
    public function sendSubscriptionEmail(EmailService $emailService, TranslatorInterface $translator, LoggerInterface $logger): Response
    {
        $dmEm = $this->getEm('dm');
        $qbIssuesReleasedThroughSubscriptionsToday = ($dmEm->createQueryBuilder())
            ->select('CONCAT(issues.pays, \'/\', issues.magazine) AS publicationCode, issues.numero AS issueNumber, issues.idUtilisateur AS userId')
            ->from(Numeros::class, 'issues')
            ->andWhere('issues.dateajout LIKE :today')
            ->setParameter('today', (new DateTime())->format('Y-m-d').'%')
            ->andWhere('issues.abonnement = 1');

        $issuesReleasedThroughSubscriptionsToday = $qbIssuesReleasedThroughSubscriptionsToday->getQuery()->getArrayResult();

        if (empty($issuesReleasedThroughSubscriptionsToday)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $userIdsList = implode(',', array_map(function($issue) {
            return $issue['userId'];
        }, $issuesReleasedThroughSubscriptionsToday));
        $users = ($dmEm->createQueryBuilder())
            ->select('users')
            ->from(Users::class, 'users')
            ->where("users.id IN ($userIdsList)")
            ->indexBy('users', 'users.id')
            ->getQuery()->getResult();

        $publicationCodesList = implode(',', array_map(function($issue) {
            return "'{$issue['publicationCode']}'";
        }, $issuesReleasedThroughSubscriptionsToday));
        $publicationNames = ($this->getEm('coa')->createQueryBuilder())
            ->select('publications.publicationcode, publications.title')
            ->from(InducksPublication::class, 'publications')
            ->where("publications.publicationcode IN ($publicationCodesList)")
            ->indexBy('publications', 'publications.publicationcode')
            ->getQuery()->getArrayResult();

        foreach($issuesReleasedThroughSubscriptionsToday as $issue) {
            ['userId' => $userId, 'publicationCode' => $publicationCode, 'issueNumber' => $issueNumber] = $issue;
            [$issue['publicationCode'] => $publicationName] = $publicationNames;
            $user = $users[$userId];
            $logger->info("Sending email to user $userId as issue $publicationCode $issueNumber has been released");
            $emailService->send(new SubscriptionIssueAdded($translator, $user, $publicationName['title'], $issueNumber));
        }

        return new Response('OK');
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/emails/pending")
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function sendPendingEmails(Request $request, EmailService $emailService, TranslatorInterface $translator, LoggerInterface $logger): Response
    {
        $dmEm = $this->getEm('dm');
        static $medalLevels = [
            'photographe' => [1 => 50, 2 => 150, 3 => 600],
            'createur'    => [1 => 20, 2 =>  70, 3 => 150],
            'duckhunter'  => [1 =>  1, 2 =>   3, 3 =>   5]
        ];

        $emailsSent = [];
        foreach(array_keys($medalLevels) as $contributionType) {
            $pendingEmailContributionsForType = $dmEm->getRepository(UsersContributions::class)->findBy([
                'contribution' => $contributionType,
                'emailsSent' => false
            ], [
                'user' => 'ASC',
                'pointsTotal' => 'ASC'
            ]);
            if (empty($pendingEmailContributionsForType)) {
                $logger->info("No email to send for contribution $contributionType");
            }
            else {
                /** @var UsersContributions[][] $pendingEmailContributionsByUser */
                $pendingEmailContributionsByUser = array_reduce($pendingEmailContributionsForType, function (array $accumulator, UsersContributions $contribution) {
                    $accumulator[$contribution->getUser()->getId()][] = $contribution;
                    return $accumulator;
                }, []);
                foreach($pendingEmailContributionsByUser as $userId => $pendingEmailContributionsForUser) {
                    $logger->info(count($pendingEmailContributionsForUser)." contributions pending for user $userId");
                    $initialPointsCount = $pendingEmailContributionsForUser[0]->getPointsTotal() - $pendingEmailContributionsForUser[0]->getPointsNew();
                    $finalPointsCount = $pendingEmailContributionsForUser[count($pendingEmailContributionsForUser) -1]->getPointsTotal();
                    $pointsEarned = $finalPointsCount - $initialPointsCount;

                    $medalReached = null;
                    foreach($medalLevels[$contributionType] as $medal => $medalThreshold) {
                        if ($initialPointsCount < $medalThreshold && $finalPointsCount >= $medalThreshold) {
                            $medalReached = $medal;
                        }
                    }
                    foreach($pendingEmailContributionsForUser as $pendingEmail) {
                        $pendingEmail->setEmailsSent(true);
                        $dmEm->persist($pendingEmail);
                    }
                    $dmEm->flush();

                    /** @var Users $user */
                    $user = $dmEm->getRepository(Users::class)->find($userId);

                    switch($contributionType) {
                        case 'duckhunter':
                            $message = new BookstoreApproved(
                                $translator, $request->getLocale(), $user, $medalReached
                            );
                        break;
                        case 'photographe':
                            $message = new EdgesPublished(
                                $translator, $request->getLocale(), $user, count($pendingEmailContributionsForUser), $pointsEarned, $medalReached
                            );
                            break;
                    }
                    if (isset($message)) {
                        $emailsSent []= $message;
                        $emailService->send($message);
                    }
                }
            }
        }
        return new JsonResponse([
            'emails_sent' => array_map(function(AbstractEmail $emailHelper) {
                return [
                    'to' => $emailHelper->getTo(),
                    'subject' => $emailHelper->getSubject()
                ];
            }, $emailsSent)
        ]);
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/bookstore/suggest")
     */
    public function suggestBookstore(Request $request, EmailService $emailService, TranslatorInterface $translator): Response
    {
        $dmEm = $this->getEm('dm');
        $userId = $this->getSessionUser()['id'];
        if (is_null($userId)) {
            $user = (new Users())
                ->setUsername('anonymous');
        }
        else {
            $user = $dmEm->getRepository(Users::class)->find($userId);
        }
        if ($request->request->has('name')) {
            $bookstore = (new Bouquineries())
                ->setIdUtilisateur($userId)
                ->setNom($request->request->get('name'))
                ->setAdressecomplete($request->request->get('address'))
                ->setCommentaire($request->request->get('comment'))
                ->setCoordx($request->request->get('coordX'))
                ->setCoordy($request->request->get('coordY'))
                ->setActif(false);
            $dmEm->persist($bookstore);
            $dmEm->flush();
        }

        $emailService->send(new BookstoreSuggested($translator, $user));

        return new Response();
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/bookstore/approve")
     * @throws ORMException
     * @throws Exception
     */
    public function approveBookstore(Request $request, ContributionService $contributionService): Response
    {
        $dmEm = $this->getEm('dm');
        $bookstoreId = $request->request->get('id');
        /** @var Bouquineries $bookstore */
        $bookstore = $dmEm->getRepository(Bouquineries::class)->find($bookstoreId);

        /** @var Users $user */
        $user = $dmEm->getRepository(Users::class)->find($bookstore->getIdUtilisateur());

        $bookstore
            ->setActif(true)
            ->setDateajout(new DateTime());

        $contributionService->persistContribution(
            $user,
            'duckhunter',
            1,
            null,
            $bookstore
        );

        $dmEm->persist($bookstore);
        $dmEm->flush();

        return new Response();
    }

    /**
     * @Route(methods={"GET"}, path="/ducksmanager/users")
     * @throws Exception
     */
    public function getAllUserNames(): Response
    {
        $dmEm = $this->getEm('dm');
        $users = $dmEm->getRepository(Users::class)->findAll();

        return new JsonResponseFromObject([
            'users' => array_map(function(Users $user) {
                return [
                    'id' => $user->getId(),
                    'username' => $user->getUsername()
                ];
            }, $users)
        ]);
    }

    /**
     * @Route(methods={"GET"}, path="/ducksmanager/users/count")
     * @throws Exception
     */
    public function getUsernameCount(): Response
    {
        $dmEm = $this->getEm('dm');
        return new JsonResponseFromObject([
            'count' => $dmEm->getRepository(Users::class)->count([])
        ]);
    }

    /**
     * @Route(methods={"GET"}, path="/ducksmanager/users/collection/rarity")
     */
    public function getCollectionRarityScores() : JsonResponse {
        $dmEm = $this->getEm('dm');
        $rsm = (new ResultSetMapping())
            ->addScalarResult('userId', 'userId', 'integer')
            ->addScalarResult('averageRarity', 'averageRarity', 'integer');
        $scoreQuery = $dmEm->createNativeQuery('
            SELECT ID_Utilisateur AS userId, round(sum(rarity)) AS averageRarity
            FROM numeros
            LEFT JOIN
                (
                    select issuecode, pow(:userCount / count(*), 1.5) / 10000 as rarity
                    from numeros n1
                    group by issuecode
                ) AS issues_rarity ON numeros.issuecode = issues_rarity.issuecode
            GROUP BY ID_Utilisateur
            ORDER BY averageRarity
        ', $rsm);
        $userCountResponse = json_decode($this->getUsernameCount()->getContent());
        $scoreQuery->setParameter('userCount', $userCountResponse->count);
        return new JsonResponseFromObject($scoreQuery->getArrayResult());
    }

    /**
     * @Route(methods={"GET"}, path="/ducksmanager/user/{username}")
     * @throws Exception
     */
    public function getDmUser(string $username): Response
    {
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => $username
        ]);
        return isset($user) ? new JsonResponseFromObject($user) : new Response('KO', 200);
    }

    /**
     * @Route(methods={"DELETE"}, path="/ducksmanager/user/{username}")
     * @throws Exception
     */
    public function deleteDmUser(string $username): Response
    {
        /** @var Users */
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => $username
        ]);
        $this->deleteUserData($user);
        $this->getEm('dm')->remove($user);

        return new Response('OK', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/user/{username}/empty")
     * @throws Exception
     */
    public function emptyDmUserIssues(string $username): Response
    {
        $this->deleteUserData($this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => $username
        ]), true);
        return new Response('OK', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/user/{username}")
     * @throws Exception
     */
    public function updateDmUser(string $username, Request $request, LoggerInterface $logger): Response
    {
        $logger->info('Content : ' . print_r($request->request->all(), true));
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => $username
        ]);
        if (!empty($email = $request->request->get('email'))) {
            $user->setEmail($email);
        }
        if (!empty($password = $request->request->get('password'))) {
            $user->setPassword($password);
        }
        if ($request->request->has('isShareEnabled')) {
            $user->setAccepterpartage($request->request->get('isShareEnabled'));
        }
        if ($request->request->has('isVideoShown')) {
            $user->setAffichervideo($request->request->get('isVideoShown'));
        }

        $this->getEm('dm')->persist($user);
        $this->getEm('dm')->flush();

        return new Response('OK', Response::HTTP_OK);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/ducksmanager/bookstore/list/{filter}",
     *     defaults={"filter"="all"}
     * )
     * @throws Exception
     */
    public function getActiveBookstores(string $filter): Response
    {
        /** @var QueryBuilder */
        $qb = $this->getEm('dm')->createQueryBuilder();
        $qb->select('bookstores.id, bookstores.nom AS name, bookstores.adressecomplete AS address, bookstores.commentaire AS comment, bookstores.coordx AS coordX, bookstores.coordy AS coordY, bookstores.dateajout AS creationDate, bookstores.actif AS active, users.username')
            ->from(Bouquineries::class, 'bookstores')
            ->leftJoin(Users::class, 'users', Join::WITH, 'bookstores.idUtilisateur = users.id');

        if ($filter === 'active') {
            $qb->where($qb->expr()->eq('bookstores.actif', $qb->expr()->literal('1')));
        }

        return new JsonResponseFromObject(array_map(
            function (array $result) {
                $result['creationDate'] = $result['creationDate']->format('Y-m-d');
                return $result;
            },
            $qb->getQuery()->getArrayResult()
        ));
    }

    private function checkNewUser(TranslatorInterface $translator, ?string $username, string $password, string $password2) : ?string
    {
        if (isset($username)) {
            if (!preg_match('#^[-_A-Za-z0-9]{3,15}$#', $username) === 0) {
                return $translator->trans('UTILISATEUR_INVALIDE');
            }
            if (strlen($password) <6) {
                return $translator->trans('MOT_DE_PASSE_6_CHAR_ERREUR');
            }
            if ($password !== $password2) {
                return $translator->trans('MOTS_DE_PASSE_DIFFERENTS');
            }
            if ($this->usernameExists($username)) {
                return $translator->trans('UTILISATEUR_EXISTANT');
            }
        }
        return null;
    }

    private function usernameExists(string $username): bool
    {
        $dmEm = $this->getEm('dm');
        $existingUser = $dmEm->getRepository(Users::class)->findBy([
            'username' => $username
        ]);
        return count($existingUser) > 0;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function createUserNoCheck(string $username, string $password, string $email): bool
    {
        $dmEm = $this->getEm('dm');

        $user = new Users();
        $user->setUsername($username);
        $user->setPassword(sha1($password));
        $user->setEmail($email);
        $user->setDateinscription(new DateTime());
        $user->setDernieracces(new DateTime());

        $dmEm->persist($user);
        $dmEm->flush();

        return true;
    }

    private function deleteUserData(Users $user, $issuesOnly = false): bool
    {
        $dmEm = $this->getEm('dm');

        $qb = $dmEm->createQueryBuilder();
        $qb->delete(Numeros::class, 'issues')
            ->where($qb->expr()->eq('issues.idUtilisateur', ':userId'))
            ->setParameter(':userId', $user->getId());
        $qb->getQuery()->execute();

        if ($issuesOnly) {
            return true;
        }

        $qb = $dmEm->createQueryBuilder();
        $qb->delete(Achats::class, 'purchases')
            ->where($qb->expr()->eq('purchases.idUser', ':userId'))
            ->setParameter(':userId', $user->getId());
        $qb->getQuery()->execute();

        $qb = $dmEm->createQueryBuilder();
        $qb->delete(UsersOptions::class, 'usersOptions')
            ->where($qb->expr()->eq('usersOptions.user', ':userId'))
            ->setParameter(':userId', $user);
        $qb->getQuery()->execute();

        $qb = $dmEm->createQueryBuilder();
        $qb->delete(AuteursPseudos::class, 'authorsUsers')
            ->where($qb->expr()->eq('authorsUsers.idUser', ':userId'))
            ->setParameter(':userId', $user->getId());
        $qb->getQuery()->execute();

        return true;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function resetBookcaseOptions(Users $user): bool
    {
        $dmEm = $this->getEm('dm');

        $user->setBibliothequeTexture1('bois');
        $user->setBibliothequeSousTexture1('HONDURAS MAHOGANY');
        $user->setBibliothequeTexture2('bois');
        $user->setBibliothequeSousTexture2('KNOTTY PINE');
        $user->setBibliothequeAfficherdoubles(true);

        $dmEm->persist($user);
        $dmEm->flush();

        return true;
    }

}
