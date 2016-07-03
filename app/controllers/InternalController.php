<?php

namespace Wtd;

use Exception;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
                $sql = "SELECT username FROM users WHERE username = ?";
                try {
                    $existingUser = self::getConnection($app)->fetchAssoc($sql, [$username]);
                    if ($existingUser) {
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
                        $checkResult = self::callInternal($app, '/user/exists', 'GET', [$username])->isSuccessful();
                        if (!$checkResult) {
                            $error='UTILISATEUR_EXISTANT';
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

                $sql = "SELECT ID FROM users WHERE username = ? AND password = ?";
                try {
                    $existingUser = self::getConnection($app)->fetchAssoc($sql, [$username, sha1($password)]);
                    if ($existingUser) {
                        return new Response($existingUser['ID'], Response::HTTP_OK);
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
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $email = $request->request->get('email');

            $sql='INSERT INTO users(username,password,Email,DateInscription) VALUES(?, ?, ?, ?)';
            try {
                self::getConnection($app)->executeQuery($sql, [$username, $password, $email, date('Y-m-d')]);
            }
            catch (Exception $e) {
                return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return new Response('OK', Response::HTTP_CREATED);
        });

        $routing->put('/internal/collection/add', function (Request $request, Application $app) {
            $country = $request->request->get('country');
            $publication = $request->request->get('publication');
            $issuenumber = $request->request->get('issuenumber');
            $condition = $request->request->get('condition');
            $userId = self::getSessionUser($app)['id'];

            $sql='INSERT INTO numeros(Pays, Magazine, Numero, Etat, ID_Acquisition, ID_Utilisateur)
                  VALUES (?, ?, ?, ?, ?, ?)';
            try {
                self::getConnection($app)->executeQuery($sql, [$country, $publication, $issuenumber, $condition, -1, $userId]);
            }
            catch (Exception $e) {
                return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new Response('OK', Response::HTTP_CREATED);
        });
    }
}
