<?php

namespace DmServer;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StatsController extends AppController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/stats/watchedauthors',
            function (Application $app, Request $request) {
                return AppController::return500ErrorOnException($app, function() use ($app) {
                    return new JsonResponse(
                        ModelHelper::getUnserializedArrayFromJson(
                            self::callInternal($app, '/stats/watchedauthors', 'GET')->getContent()
                        )
                    );
                });
            }
        );
    }
}
