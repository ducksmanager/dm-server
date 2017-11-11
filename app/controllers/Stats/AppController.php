<?php

namespace DmServer\Controllers\Stats;

use DmServer\Controllers\AbstractController;
use DmServer\ModelHelper;
use Stats\Contracts\Results\IssueListWithSuggestionDetails;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Radebatz\Silex2Swagger\Swagger\Annotations as S2S;
use Swagger\Annotations as SWG;

/**
 * @S2S\Controller(prefix="/collection/stats",
 *   @SWG\Parameter(
 *     name="x-dm-version",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Parameter(
 *     name="x-dm-user",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Parameter(
 *     name="x-dm-pass",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Response(response=200),
 *   @SWG\Response(response=401, description="User not authorized"),
 *   @SWG\Response(response="default", description="Error")
 * ),
 * @SLX\Before("DmServer\RequestInterceptor::checkVersion")
 * @SLX\Before("DmServer\RequestInterceptor::authenticateUser")
 */
class AppController extends AbstractController
{
    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="watchedauthorsstorycount")
     * )
     * @param Application $app
     * @return JsonResponse
     */
    function getWatchedAuthorStoryCount(Application $app) {
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

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="suggestedissues/{countrycode}"),
     *   @SWG\Parameter(
     *     name="countrycode",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="countrycode", regex="^(?<countrycode_regex>[a-z]+)$"),
     *	 @SLX\Value(variable="countrycode", default="ALL")
     * )
     * @param Application $app
     * @param string $countrycode
     * @return JsonResponse
     */
    function getSuggestedIssues(Application $app, $countrycode) {
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
}
