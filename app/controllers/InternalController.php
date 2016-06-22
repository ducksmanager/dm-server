<?php

namespace Wtd;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                if (false) { // TODO
                    return new Response('', 409);
                }
                return new Response('', 200);
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
                    return new Response('OK', 200);
                }

                return new Response(self::translate($app, $error), 403);
            }
        );

        $routing->post('/internal/user/new', function (Request $request, Application $app) {
            // TODO
            return new Response('OK', 201);
        });
    }
}
