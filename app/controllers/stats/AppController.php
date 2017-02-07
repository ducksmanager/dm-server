<?php

namespace DmServer\Controllers\Stats;

use DmServer\Controllers\AbstractController;
use DmServer\ModelHelper;
use DmStats\Contracts\Results\IssueListWithSuggestionDetails;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AppController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/collection/stats/watchedauthorsstorycount',
            function (Application $app, Request $request) {
                return AbstractController::return500ErrorOnException($app, function() use ($app) {
                    $authorsAndStoryCount = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/authorsstorycount', 'GET')->getContent()
                    );
                    $authorsAndStoryMissingForUserCount = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/authorsstorycount/usercollection/missing', 'GET')->getContent()
                    );
                    $authorsFullNames = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/coa/authorsfullnames', 'GET', [implode(',', array_keys($authorsAndStoryCount))])->getContent()
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
        $routing->get(
            '/collection/stats/suggestedpublications',
            function (Application $app, Request $request) {
                return AbstractController::return500ErrorOnException($app, function() use ($app) {
                    $suggestedPublications = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/suggestedpublications', 'GET')->getContent()
                    );

                    $publicationAuthors = array_map(function ($publication) {
                        return $publication['personcode'];
                    }, $suggestedPublications);

                    IssueListWithSuggestionDetails::$authors = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal(
                            $app, '/coa/authorsfullnames', 'GET', [implode(',', $publicationAuthors)]
                        )->getContent()
                    );

                    $storyCodes = array_map(function ($publication) {
                        return $publication['storycode'];
                    }, $suggestedPublications);

                    IssueListWithSuggestionDetails::$storyDetails = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal(
                            $app, '/coa/storydetails', 'GET', [implode(',', $storyCodes)]
                        )->getContent()
                    );

                    $publicationList = new IssueListWithSuggestionDetails();
                    foreach($suggestedPublications as $publication) {
                        $publicationList->addStory($publication['publicationcode'], $publication['issuenumber'], $publication['personcode'], $publication['storycode']);
                    }

                    return new JsonResponse($publicationList->getStories());
                });
            }
        );
    }
}
