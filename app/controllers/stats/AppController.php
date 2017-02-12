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
            '/collection/stats/suggestedissues',
            function (Application $app, Request $request) {
                return AbstractController::return500ErrorOnException($app, function() use ($app) {
                    $suggestedStories = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/stats/suggestedissues', 'GET')->getContent()
                    );

                    $storyAuthors = array_map(function ($story) {
                        return $story['personcode'];
                    }, $suggestedStories);

                    IssueListWithSuggestionDetails::$authors = ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal(
                            $app, '/coa/authorsfullnames', 'GET', [implode(',', $storyAuthors)]
                        )->getContent()
                    );

                    $storyCodes = array_map(function ($story) {
                        return $story['storycode'];
                    }, $suggestedStories);

                    $storyCodesChunks = array_chunk($storyCodes, 50);

                    foreach($storyCodesChunks as $storyCodesChunk) {
                        IssueListWithSuggestionDetails::$storyDetails = array_merge(
                            IssueListWithSuggestionDetails::$storyDetails,
                            ModelHelper::getUnserializedArrayFromJson(
                                self::callInternal(
                                    $app, '/coa/storydetails', 'GET', [implode(',', $storyCodesChunk)]
                                )->getContent()
                            )
                        );
                    }

                    $storyList = new IssueListWithSuggestionDetails();
                    foreach($suggestedStories as $story) {
                        $storyList->addStory($story['publicationcode'], $story['issuenumber'], $story['personcode'], $story['storycode'], $story['score']);
                    }

                    return new JsonResponse([
                        'maxScore' => $suggestedStories[0]['score'],
                        'minScore' => $suggestedStories[count($suggestedStories) -1]['score'],
                        'stories' => json_decode(json_encode($storyList->getStories()))
                    ]);
                });
            }
        );
    }
}
