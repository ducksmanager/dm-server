<?php

namespace DmServer\Controllers\Rawsql;

use DmServer\Controllers\AbstractController;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class AppController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post(
            '/rawsql',
            function (Application $app, Request $request) {
                return self::callInternal($app, '/rawsql', 'POST', [
                    'query' => $request->request->get('query'),
                    'db' => $request->request->get('db')
                ]);
            }
        );
    }
}
