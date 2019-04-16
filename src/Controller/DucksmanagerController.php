<?php
namespace App\Controller;

use App\Entity\Dm\Achats;
use App\Entity\Dm\AuteursPseudos;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersPasswordTokens;
use App\Helper\collectionUpdateHelper;
use App\Helper\CsvHelper;
use App\Helper\Email\EdgesPublishedEmail;
use App\Helper\Email\ResetPasswordEmail;
use App\Helper\Email\UserSuggestedBookstoreEmail;
use App\Helper\JsonResponseFromObject;
use DateTime;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\OrderBy;
use Exception;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DucksmanagerController extends AbstractController
{
    use collectionUpdateHelper;

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
    public function resetPasswordInit(Request $request, LoggerInterface $logger, Swift_Mailer $mailer, TranslatorInterface $translator): Response
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

        $message = new ResetPasswordEmail($mailer, $translator, $user, $token);
        $message->send();
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
     * @throws ORMException
     * @throws MappingException
     */
    public function resetDemo() {
        $dmEm = $this->getEm('dm');
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

            $demoUserIssueData = CsvHelper::readCsv('demo_user/issues.csv');

            foreach ($demoUserIssueData as $publicationData) {
                $this->addOrChangeIssues(
                    $dmEm,
                    $demoUser->getId(),
                    $publicationData['publicationCode'],
                    $publicationData['issueNumbers'],
                    $publicationData['condition'],
                    null,
                    null
                );
            }

            $demoUserPurchaseData = CsvHelper::readCsv('demo_user/purchases.csv');

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
     * @Route(methods={"POST"}, path="/ducksmanager/email/bookstore")
     */
    public function sendBookstoreEmail(Request $request, Swift_Mailer $mailer): Response
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

        $message = new UserSuggestedBookstoreEmail($mailer, $user);
        $message->send();

        return new Response();
    }

    /**
     * @Route(methods={"POST"}, path="/ducksmanager/email/confirmation")
     */
    public function sendConfirmationEmail(Request $request, Swift_Mailer $mailer, TranslatorInterface $translator): Response
    {
        $dmEm = $this->getEm('dm');
        $userId = $request->request->get('userid');
        $emailType = $request->request->get('type');

        /** @var Users $user */
        $user = $dmEm->getRepository(Users::class)->find($userId);

        if (is_null($user)) {
            return new Response("User with ID $userId was not found", Response::HTTP_BAD_REQUEST);
        }

        $details = $request->request->get('details');
        if (!is_array($details)) {
            $details = json_decode($details, true);
        }

        switch ($emailType) {
            case 'edges_published':
                $newMedalLevel = $details['newMedalLevel'];
                $extraEdges = $details['extraEdges'];
                $extraPhotographerPoints = $details['extraPhotographerPoints'];

                $message = new EdgesPublishedEmail($mailer, $translator, $request->getLocale(), $user, $extraEdges, $extraPhotographerPoints, $newMedalLevel);
                break;
            default:
                return new Response("Invalid email type : $emailType", Response::HTTP_BAD_REQUEST);

        }
        $message->send();

        return new Response();
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
        $user->setBibliothequeGrossissement(1.5);

        $dmEm->persist($user);
        $dmEm->flush();

        return true;
    }

}
