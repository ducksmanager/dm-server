<?php
namespace App\Controller;

use App\Entity\Dm\Achats;
use App\Entity\Dm\AuteursPseudos;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Bouquineries;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersContributions;
use App\Entity\Dm\UsersPasswordTokens;
use App\Helper\Email\AbstractEmail;
use App\Helper\Email\BookstoreApprovedEmail;
use App\Helper\Email\BookstoreSuggestedEmail;
use App\Helper\Email\EdgesPublishedEmail;
use App\Helper\Email\ResetPasswordEmail;
use App\Helper\JsonResponseFromObject;
use App\Service\CollectionUpdateService;
use App\Service\ContributionService;
use App\Service\CsvService;
use App\Service\EmailService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\OrderBy;
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

        $emailService->send(new ResetPasswordEmail($translator, $user, $token));
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
     * @Route(methods={"GET"}, path="/ducksmanager/bookcase/{userId}/sort")
     * @throws ORMException
     */
    public function getBookcaseSorting(int $userId): JsonResponse
    {
        $maxSort = json_decode($this->getLastPublicationPosition($userId)->getContent())->max;

        $dmEm = $this->getEm('dm');
        $qbMissingSorts = $dmEm->createQueryBuilder();
        $qbMissingSorts
            ->select('distinct concat(issues.pays, \'/\', issues.magazine) AS missing_publication_code')
            ->from(Numeros::class, 'issues')

            ->andWhere('concat(issues.pays, \'/\', issues.magazine) not in (select sorts.publicationcode from '.BibliothequeOrdreMagazines::class.' sorts where sorts.idUtilisateur = :userId)')
            ->andWhere('issues.idUtilisateur = :userId')
            ->setParameter(':userId', $userId)

            ->orderBy(new OrderBy('missing_publication_code', 'ASC'));

        $missingSorts = $qbMissingSorts->getQuery()->getArrayResult();
        foreach($missingSorts as $missingSort) {
            $sort = new BibliothequeOrdreMagazines();
            $sort->setPublicationcode($missingSort['missing_publication_code']);
            $sort->setOrdre(++$maxSort);
            $sort->setIdUtilisateur($userId);
            $dmEm->persist($sort);
        }
        $dmEm->flush();

        $sorts = $dmEm->getRepository(BibliothequeOrdreMagazines::class)->findBy(
            ['idUtilisateur' => $userId],
            ['ordre' => 'ASC']
        );

        return new JsonResponse(array_map(function(BibliothequeOrdreMagazines $sort) {
            return $sort->getPublicationcode();
        }, $sorts));
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
                            $message = new BookstoreApprovedEmail(
                                $translator, $request->getLocale(), $user, $medalReached
                            );
                        break;
                        case 'photographe':
                            $message = new EdgesPublishedEmail(
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
        $userId = $request->request->get('userId');
        if (is_null($userId)) {
            $user = new Users();
            $user->setUsername('anonymous');
        }
        else {
            $user = $dmEm->getRepository(Users::class)->find($userId);
        }

        $emailService->send(new BookstoreSuggestedEmail($translator, $user));

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
        [$coordX, $coordY] = $request->request->get('coordinates');
        /** @var Bouquineries $bookstore */
        $bookstore = $dmEm->getRepository(Bouquineries::class)->find($bookstoreId);

        /** @var Users $user */
        $user = $dmEm->getRepository(Users::class)->find($bookstore->getIdUtilisateur());

        $bookstore
            ->setCoordx($coordX)
            ->setCoordy($coordY)
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

    private function deleteUserData(Users $user): bool
    {
        $dmEm = $this->getEm('dm');
        $qb = $dmEm->createQueryBuilder();

        $qb->delete(Numeros::class, 'issues')
            ->where($qb->expr()->eq('issues.idUtilisateur', ':userId'))
            ->setParameter(':userId', $user->getId());
        $qb->getQuery()->execute();

        $qb = $dmEm->createQueryBuilder();
        $qb->delete(Achats::class, 'purchases')
            ->where($qb->expr()->eq('purchases.idUser', ':userId'))
            ->setParameter(':userId', $user->getId());
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

        $dmEm->persist($user);
        $dmEm->flush();

        return true;
    }

}
