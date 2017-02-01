<?php

namespace DmServer\Controllers\User;

use DmServer\Controllers\AbstractController;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post(
            '/user/new',
            function (Application $app, Request $request) {
                $check = self::callInternal($app, '/user/new/check', 'GET', [
                    $request->request->get('username'),
                    $request->request->get('password'),
                    $request->request->get('password2')
                ]);
                if ($check->getStatusCode() !== Response::HTTP_OK) {
                    return $check;
                } else {
                    return self::callInternal($app, '/user/new', 'PUT', [
                        'username' => $request->request->get('username'),
                        'password' => $request->request->get('password'),
                        'email' => $request->request->get('email')
                    ]);
                }

            }
        );
    }
}
