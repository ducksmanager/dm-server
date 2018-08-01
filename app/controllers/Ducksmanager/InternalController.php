<?php

namespace DmServer\Controllers\Ducksmanager;

use Dm\Models\Achats;
use Dm\Models\AuteursPseudos;

use Dm\Models\Numeros;
use Dm\Models\Users;

use DmServer\Controllers\AbstractInternalController;
use DmServer\DmServer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use RuntimeException;
use Silex\Application;

use Swift_Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;

/**
 * @SLX\Controller(prefix="/internal/ducksmanager")
 */
class InternalController extends AbstractInternalController
{
    /**
     * @param Application $app
     * @param callable    $function
     * @return mixed|Response
     */
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM, $function);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="exists/{username}"),
     * )
     * @param Application $app
     * @param string      $username
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function getUsernameExists(Application $app, $username) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($username) {
            $existingUser = $dmEm->getRepository(Users::class)->findBy([
                'username' => $username
            ]);
            if (count($existingUser) > 0) {
                return new Response('', Response::HTTP_OK);
            }
            return new Response('', Response::HTTP_NO_CONTENT);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="new/check/{username}/{password}/{password2}"),
     * )
     * @param Application $app
     * @param string      $username
     * @param string      $password
     * @param string      $password2
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function checkNewUser(Application $app, $username, $password, $password2) {
        $error = null;

        if (isset($username)) {
            if (strlen($username) <3) {
                $error='UTILISATEUR_3_CHAR_ERREUR';
            }
            if (strlen($password) <6) {
                $error='MOT_DE_PASSE_6_CHAR_ERREUR';
            }
            elseif ($password !== $password2) {
                $error='MOTS_DE_PASSE_DIFFERENTS';
            }
            else {
                if (self::callInternal($app, '/ducksmanager/exists', 'GET', [$username])
                        ->getStatusCode() !== Response::HTTP_NO_CONTENT) {
                    return new Response(self::$translator->trans('UTILISATEUR_EXISTANT'), Response::HTTP_CONFLICT);
                }
            }
        }
        if (is_null($error)) {
            return new Response('OK', Response::HTTP_OK);
        }

        return new Response(self::$translator->trans($error), Response::HTTP_PRECONDITION_FAILED);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="user/get/{username}/{password}"),
     * )
     * @param Application $app
     * @param string      $username
     * @param string      $password
     * @return Response
     * @throws \Exception
     */
    public function getUser(Application $app, $username, $password) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($username, $password) {
            /** @var Users $existingUser */
            $privilegeQb = $dmEm->createQueryBuilder();
            $qb = $dmEm->createQueryBuilder();
            $qb
                ->select('DISTINCT u')
//                ->addSelect('('.
//                    $privilegeQb
//                        ->select('u_permissions.privilege')
//                        ->from(UsersPermissions::class, 'u_permissions')
//                        ->where($qb->expr()->andX(
//                            $qb->expr()->eq('u.username', 'u_permissions.username'),
//                            $qb->expr()->eq('u_permissions.role', ':ec_role')
//                        ))
//                        ->getDQL()
//                .') AS privilege')
                ->from(Users::class, 'u')
                ->andWhere($qb->expr()->eq('u.username', ':username'))
                ->andWhere($qb->expr()->eq('u.password', ':password'));

            $qb->setParameters([':username' => $username, 'password' => $password]);

            try {
                $existingUser = $qb->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);
                if (!is_null($existingUser)) {
                    return new JsonResponse($existingUser, Response::HTTP_OK);
                } else {
                    return new Response('', Response::HTTP_UNAUTHORIZED);
                }
            }
            catch(NoResultException|NonUniqueResultException $e) {
                return new Response('', Response::HTTP_UNAUTHORIZED);
            }
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="PUT", uri="new"),
     * )
     * @param Request     $request
     * @param Application $app
     * @return Response
     * @throws \Exception
     */
    public function createUser(Request $request, Application $app) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($request) {
            $user = new Users();
            $user->setUsername($request->request->get('username'));
            $user->setPassword(sha1($request->request->get('password')));
            $user->setEmail($request->request->get('email'));
            $user->setDateinscription(new \DateTime());

            $dmEm->persist($user);
            $dmEm->flush();

            return new Response('OK', Response::HTTP_CREATED);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="DELETE", uri="{userId}/data"),
     * )
     * @param Application $app
     * @param string      $userId
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function deleteUserData(Application $app, $userId) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($userId) {
            $qb = $dmEm->createQueryBuilder();

            $qb->delete(Numeros::class, 'issues')
                ->where($qb->expr()->eq('issues.idUtilisateur', ':userId'))
                ->setParameter(':userId', $userId);
            $qb->getQuery()->execute();

            $qb = $dmEm->createQueryBuilder();
            $qb->delete(Achats::class, 'purchases')
                ->where($qb->expr()->eq('purchases.idUser', ':userId'))
                ->setParameter(':userId', $userId);
            $qb->getQuery()->execute();

            $qb = $dmEm->createQueryBuilder();
            $qb->delete(AuteursPseudos::class, 'authorsUsers')
                ->where($qb->expr()->eq('authorsUsers.idUser', ':userId'))
                ->setParameter(':userId', $userId);
            $qb->getQuery()->execute();

            return new Response('OK');
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="{userId}/data/bookcase/reset"),
     * )
     * @param Application $app
     * @param string      $userId
     * @return Response
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function resetBookcaseOptions(Application $app, $userId) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($userId) {
            $user = $dmEm->getRepository(Users::class)->findOneBy([
                'id' => $userId
            ]);

            $user->setBibliothequeTexture1('bois');
            $user->setBibliothequeSousTexture1('HONDURAS MAHOGANY');
            $user->setBibliothequeTexture2('bois');
            $user->setBibliothequeSousTexture2('KNOTTY PINE');
            $user->setBibliothequeGrossissement(1.5);

            $dmEm->persist($user);
            $dmEm->flush();

            return new Response('OK');
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="POST", uri="email/bookstore")
     * )
     * @param Application $app
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function sendBookstoreEmail(Application $app, Request $request) {
        return self::wrapInternalService($app, function(EntityManager $dmEm) use ($app, $request) {
            $userId = $request->request->get('userId');
            if (is_null($userId)) {
                $userName = 'anonymous';
            }
            else {
                $userName = $dmEm->getRepository(Users::class)->findOneBy([
                    'id' => $userId
                ])->getUsername();
            }

            $message = new \Swift_Message();
            $message
                ->setSubject('Ajout de bouquinerie')
                ->setFrom([$userName. '@' .DmServer::$settings['smtp_origin_email_domain_ducksmanager']])
                ->setTo([DmServer::$settings['smtp_username']])
                ->setBody('<a href="https://www.ducksmanager.net/backend/bouquineries.php">Validation</a>', 'text/html');

            /** @var Swift_Mailer $mailer */
            $mailer = $app['mailer'];
            $failures = [];
            // Pass a variable name to the send() method
            if (!$mailer->send($message, $failures)) {
                throw new RuntimeException("Can't send e-mail '$message': failed with ".print_r($failures, true));
            }

            return new Response(Response::HTTP_OK);
        });
    }
}
