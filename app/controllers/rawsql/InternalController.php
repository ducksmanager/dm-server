<?php

namespace DmServer\Controllers\Rawsql;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->post(
            '/internal/rawsql',
            function (Request $request, Application $app) {
                return AbstractController::return500ErrorOnException($app, function() use ($request, $app) {
                    $query = $request->request->get('query');
                    $db = $request->request->get('db');

                    $em = DmServer::getEntityManager($db);
                    if (is_null($em)) {
                        return new Response('Invalid parameter : db='.$db, Response::HTTP_BAD_REQUEST);
                    }
                    if (strpos($query, ';') !== false) { // In lack of something better
                        return new Response('Raw queries shouldn\'t contain the ";" symbol', Response::HTTP_BAD_REQUEST);
                    }

                    if (isset($app['monolog'])) {
                        $app['monolog']->addInfo('Raw sql sent : '.$query);
                    }
                    $results = $em->getConnection()->fetchAll($query);
                    return new JsonResponse($results);
                });
            }
        );
    }
}
