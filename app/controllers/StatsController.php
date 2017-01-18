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
            '/stats/watchedauthorsstorycount',
            function (Application $app, Request $request) {
                return AppController::return500ErrorOnException($app, function() use ($app) {
                    $authorsAndStoryCount = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/authorsstorycount', 'GET')->getContent()
                    );
                    $authorsFullNames = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/authorsfullnames', 'GET', [implode(',', array_keys($authorsAndStoryCount))])->getContent()
                    );

                    $watchedAuthorsStoryCount = [];
                    array_walk($authorsAndStoryCount, function($storyCount, $personCode) use(&$watchedAuthorsStoryCount, $authorsFullNames) {
                        $watchedAuthorsStoryCount[$personCode] = [
                            'fullname' => $authorsFullNames[$personCode],
                            'storycount' => $storyCount
                        ];
                    });

                    return new JsonResponse($watchedAuthorsStoryCount);
                });
            }
        );
    }
}
