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
            '/collection/stats/watchedauthorsstorycount',
            function (Application $app, Request $request) {
                return AppController::return500ErrorOnException($app, function() use ($app) {
                    $authorsAndStoryCount = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/authorsstorycount', 'GET')->getContent()
                    );
                    $authorsAndStoryMissingForUserCount = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/authorsstorycount/usercollection/missing', 'GET')->getContent()
                    );
                    $authorsFullNames = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/authorsfullnames', 'GET', [implode(',', array_keys($authorsAndStoryCount))])->getContent()
                    );

                    $watchedAuthorsStoryCount = [];
                    array_walk($authorsFullNames, function($authorFullName, $personCode) use(&$watchedAuthorsStoryCount, $authorsAndStoryCount, $authorsAndStoryMissingForUserCount) {
                        $watchedAuthorsStoryCount[$personCode] = [
                            'fullname' => $authorFullName,
                            'missingstorycount' => $authorsAndStoryMissingForUserCount[$personCode] ?? 0,
                            'storycount' => $authorsAndStoryCount[$personCode] ?? 0
                        ];
                    });

                    return new JsonResponse($watchedAuthorsStoryCount);
                });
            }
        );
    }
}
