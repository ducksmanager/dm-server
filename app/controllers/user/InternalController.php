<?php

namespace DmServer\Controllers\User;

use Dm\Models\Users;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/user/exists/{username}',
            function (Request $request, Application $app, $username) {
                return AbstractController::return500ErrorOnException($app, function() use ($username) {
                    $existingUser = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(array(
                        'username' => $username
                    ));
                    if (count($existingUser) > 0) {
                        return new Response('', Response::HTTP_CONFLICT);
                    }
                    return new Response('', Response::HTTP_OK);
                });
            }
        );
        $routing->get(
            '/internal/user/new/check/{username}/{password}/{password2}',
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
                        if (!(self::callInternal($app, '/user/exists', 'GET', [$username])
                            ->isSuccessful())) {
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
            '/internal/user/check/{username}/{password}',
            function (Request $request, Application $app, $username, $password) {
                return AbstractController::return500ErrorOnException($app, function() use ($username, $password) {
                    /** @var Users $existingUser */
                    $existingUser = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findOneBy(array(
                        'username' => $username,
                        'password' => $password
                    ));
                    if (!is_null($existingUser)) {
                        return new Response($existingUser->getId(), Response::HTTP_OK);
                    } else {
                        return new Response('', Response::HTTP_UNAUTHORIZED);
                    }
                });
            }
        );

        $routing->put('/internal/user/new', function (Request $request, Application $app) {
            return AbstractController::return500ErrorOnException($app, function() use ($request) {
                $user = new Users();
                $user->setUsername($request->request->get('username'));
                $user->setPassword(sha1($request->request->get('password')));
                $user->setEmail($request->request->get('email'));
                $user->setDateinscription(new \DateTime());

                DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->persist($user);
                DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->flush();

                return new Response('OK', Response::HTTP_CREATED);
            });
        });
    }
}
