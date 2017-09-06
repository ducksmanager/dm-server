<?php

namespace DmServer\Controllers\User;

use DmServer\Controllers\AbstractController;
use DmServer\CsvHelper;
use DmServer\DmServer;
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
            '/user/sale/{otheruser}',
            function (Application $app, Request $request, $otheruser) {

                if (self::callInternal($app, '/ducksmanager/exists', 'GET', [$otheruser])->getStatusCode() === Response::HTTP_NO_CONTENT) {
                    return new Response(self::$translator->trans('UTILISATEUR_INVALIDE'), Response::HTTP_BAD_REQUEST);
                }

                return self::callInternal($app, "/user/sale/$otheruser", 'POST');
            }
        );

        $routing->get(
            '/user/sale/{otheruser}/{date}',
            function (Application $app, Request $request, $otheruser, $date) {
                return self::callInternal($app, "/user/sale/$otheruser/$date", 'GET');
            }
        );
    }
}
