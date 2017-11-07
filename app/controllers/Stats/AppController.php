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
                $authorsAndStoryMissingForUserCount = ModelHelper::getUnserializedArrayFromJson(
                    self::callInternal($app, '/stats/authorsstorycount/usercollection/missing', 'GET')->getContent()
                );
                $authorsAndStoryCount = ModelHelper::getUnserializedArrayFromJson(
                    self::callInternal($app,
                        '/stats/authorsstorycount/' . implode(',', array_keys($authorsAndStoryMissingForUserCount)),
                        'GET')->getContent()
                );
                $authorsFullNames = ModelHelper::getUnserializedArrayFromJson(
                    self::callInternal($app, '/coa/authorsfullnames', 'GET',
                        [implode(',', array_keys($authorsAndStoryCount))])->getContent()
                );

                $watchedAuthorsStoryCount = [];
                array_walk($authorsFullNames, function ($authorFullName, $personCode) use (
                    &$watchedAuthorsStoryCount,
                    $authorsAndStoryCount,
                    $authorsAndStoryMissingForUserCount
                ) {
                    $watchedAuthorsStoryCount[$personCode] = [
                        'fullname' => $authorFullName,
                        'missingstorycount' => $authorsAndStoryMissingForUserCount[$personCode] ?? 0,
                        'storycount' => $authorsAndStoryCount[$personCode] ?? 0
                    ];
                });

                return new JsonResponse($watchedAuthorsStoryCount);
            }
        );

        $routing->get(
            '/collection/stats/suggestedissues/{countrycode}',
            function (Application $app, Request $request, $countrycode) {
                $suggestedStories = ModelHelper::getUnserializedArrayFromJson(
                    self::callInternal($app, '/stats/suggestedissues/' . $countrycode, 'GET')->getContent()
                );

                // Get author names
                $storyAuthors = array_map(function ($story) {
                    return $story['personcode'];
                }, $suggestedStories);

                IssueListWithSuggestionDetails::$authors = self::callInternal($app, '/coa/authorsfullnames', 'GET',
                    [$storyAuthors], 50);

                // Get author names - END

                // Get story details
                $storyCodes = array_map(function ($story) {
                    return $story['storycode'];
                }, $suggestedStories);

                IssueListWithSuggestionDetails::$storyDetails = self::callInternal($app, '/coa/storydetails', 'GET',
                    [$storyCodes], 50);
                // Add author to story details
                foreach ($suggestedStories as $suggestedStory) {
                    IssueListWithSuggestionDetails::$storyDetails[$suggestedStory['storycode']]['personcode'] = $suggestedStory['personcode'];
                }

                // Get story details - END

                // Get publication titles
                $publicationTitles = array_map(function ($story) {
                    return $story['publicationcode'];
                }, $suggestedStories);

                IssueListWithSuggestionDetails::$publicationTitles = self::callInternal($app,
                    '/coa/publicationtitles', 'GET', [$publicationTitles], 50);

                // Get publication titles - END

                $storyList = new IssueListWithSuggestionDetails();
                foreach ($suggestedStories as $story) {
                    $storyList->addStory($story['publicationcode'], $story['issuenumber'], $story['storycode'],
                        $story['personcode'], $story['score']);
                }

                return new JsonResponse([
                    'maxScore' => $suggestedStories[0]['score'],
                    'minScore' => $suggestedStories[count($suggestedStories) - 1]['score'],
                    'issues' => json_decode(json_encode($storyList->getIssues())),
                    'authors' => IssueListWithSuggestionDetails::$authors,
                    'publicationTitles' => IssueListWithSuggestionDetails::$publicationTitles,
                    'storyDetails' => IssueListWithSuggestionDetails::$storyDetails
                ]);
            }
        )->value('countrycode', 'ALL');
    }
}
