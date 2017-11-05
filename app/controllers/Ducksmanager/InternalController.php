<?php

namespace DmServer\Controllers\Ducksmanager;

use Dm\Models\Achats;
use Dm\Models\AuteursPseudos;

use Dm\Models\Numeros;
use Dm\Models\Users;

use DmServer\Controllers\AbstractInternalController;
use DmServer\DmServer;

use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerCollection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalController extends AbstractInternalController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_DM, $function);
    }
    
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/ducksmanager/exists/{username}',
            function (Request $request, Application $app, $username) {
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
        );
        $routing->get(
            '/internal/ducksmanager/new/check/{username}/{password}/{password2}',
            function (Request $request, Application $app, $username, $password, $password2) {

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
        );

        $routing->get(
            '/internal/ducksmanager/check/{username}/{password}',
            function (Request $request, Application $app, $username, $password) {
                return self::wrapInternalService($app, function(EntityManager $dmEm) use ($username, $password) {
                    /** @var Users $existingUser */
                    $existingUser = $dmEm->getRepository(Users::class)->findOneBy([
                        'username' => $username,
                        'password' => $password
                    ]);
                    if (!is_null($existingUser)) {
                        return new Response($existingUser->getId(), Response::HTTP_OK);
                    } else {
                        return new Response('', Response::HTTP_UNAUTHORIZED);
                    }
                });
            }
        );

        $routing->put('/internal/ducksmanager/new', function (Request $request, Application $app) {
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
        });

        $routing->delete('/internal/ducksmanager/{userId}/data', function (Request $request, Application $app, $userId) {
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
        });

        $routing->post('/internal/ducksmanager/{userId}/data/bookcase/reset', function (Request $request, Application $app, $userId) {
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
        });
    }
}
