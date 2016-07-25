<?php

namespace Wtd;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends AppController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post(
            '/collection/new',
            function (Application $app, Request $request) {
                $check = self::callInternal($app, '/user/new/check', 'GET', [
                    $request->request->get('username'),
                    $request->request->get('password'),
                    $request->request->get('password2')
                ]);
                if ($check->getStatusCode() !== Response::HTTP_OK) {
                    return $check;
                }
                else {
                    return self::callInternal($app, '/user/new', 'PUT', [
                        'username' => $request->request->get('username'),
                        'password' => $request->request->get('password'),
                        'email' => $request->request->get('email')
                    ]);
                }

            }
        );

        $routing->post(
            '/collection/add',
            function (Application $app, Request $request) {
                return self::callInternal($app, '/collection/add', 'PUT', [
                    'country' => $request->request->get('country'),
                    'publication' => $request->request->get('publication'),
                    'issuenumber' => $request->request->get('issuenumber'),
                    'condition'   => $request->request->get('condition')
                ]);
            }
        );

        $routing->get(
            '/collection/fetch/',
            function (Application $app, Request $request) {
                return self::callInternal($app, '/collection/fetch', 'GET');
            }
        );
    }
}
