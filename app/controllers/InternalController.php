<?php

namespace Wtd;

use Exception;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wtd\Models\Numeros;
use Wtd\Models\Users;

class InternalController extends AppController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/user/exists/{username}',
            function (Request $request, Application $app, $username) {
                try {
                    $existingUser = Wtd::getEntityManager()->getRepository(Users::class)->findBy(array(
                        'username' => $username
                    ));
                    if (count($existingUser) > 0) {
                        return new Response('', Response::HTTP_CONFLICT);
                    }
                    return new Response('', Response::HTTP_OK);
                }
                catch (Exception $e) {
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
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
                            return new Response(self::translate($app, 'UTILISATEUR_EXISTANT'), Response::HTTP_CONFLICT);
                        }
                    }
                }
                if (is_null($error)) {
                    return new Response('OK', Response::HTTP_OK);
                }

                return new Response(self::translate($app, $error), Response::HTTP_PRECONDITION_FAILED);
            }
        );

        $routing->get(
            '/internal/user/check/{username}/{password}',
            function (Request $request, Application $app, $username, $password) {
                try {
                    $existingUser = Wtd::getEntityManager()->getRepository(Users::class)->findBy(array(
                        'username' => $username,
                        'password' => $password
                    ));
                    if (count($existingUser) > 0) {
                        return new Response($existingUser[0]->getId(), Response::HTTP_OK);
                    }
                    else {
                        return new Response('', Response::HTTP_UNAUTHORIZED);
                    }
                }
                catch (Exception $e) {
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        );

        $routing->put('/internal/user/new', function (Request $request, Application $app) {
            $user = new Users();
            $user->setUsername($request->request->get('username'));
            $user->setPassword(sha1($request->request->get('password')));
            $user->setEmail($request->request->get('email'));
            $user->setDateinscription(new \DateTime());

            try {
                Wtd::getEntityManager()->persist($user);
                Wtd::getEntityManager()->flush();
            }
            catch (Exception $e) {
                return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return new Response('OK', Response::HTTP_CREATED);
        });

        $routing->put('/internal/collection/add', function (Request $request, Application $app) {
            $issue = new Numeros();
            $issue->setPays($request->request->get('country'));
            $issue->setMagazine($request->request->get('publication'));
            $issue->setNumero($request->request->get('issuenumber'));
            $issue->setEtat($request->request->get('condition'));
            $issue->setIdUtilisateur(self::getSessionUser($app)['id']);

            try {
                Wtd::getEntityManager()->persist($issue);
                Wtd::getEntityManager()->flush();
            }
            catch (Exception $e) {
                return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new Response('OK', Response::HTTP_CREATED);
        });
    }
}
