<?php

namespace Wtd;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

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
                if ($check->getStatusCode() !== 200) {
                    return $check;
                }
                else {
                    return self::callInternal($app, '/user/new', 'POST', [
                        $request->request->get('username'),
                        $request->request->get('password'),
                        $request->request->get('email')
                    ]);
                }

            }
        );
    }
}
