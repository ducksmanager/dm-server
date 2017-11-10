<?php

namespace DmServer\Controllers\Coa;

use DmServer\Controllers\AbstractController;
use DmServer\ModelHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Radebatz\Silex2Swagger\Swagger\Annotations as S2S;
use Swagger\Annotations as SWG;

/**
 * @S2S\Controller(prefix="/coa",
 *   @SWG\Parameter(
 *     name="x-dm-version",
 *     in="header",
 *     required=true
 *   ),
 *   @SWG\Response(response=200),
 *   @SWG\Response(response="default", description="Error")
 * ),
 * @SLX\Before("DmServer\RequestInterceptor::checkVersion")
 */
class AppController extends AbstractController
{
    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="list/countries")
     * )
     * @param Application $app
     * @return JsonResponse
     */
    public function listCountries(Application $app) {
        return new JsonResponse(
            ModelHelper::getUnserializedArrayFromJson(
                self::callInternal($app, '/coa/countrynames', 'GET', [])->getContent()
            )
        );
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="list/countries/{countries}"),
     *   @SWG\Parameter(
     *     name="countries",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="countries", regex="^((?<countrycode_regex>[a-z]+),){0,9}(?&countrycode_regex)$")
     * )
     * @param Application $app
     * @param string $countries
     * @return JsonResponse
     */
    public function listCountriesFromCodes(Application $app, $countries) {
        return new JsonResponse(
            ModelHelper::getUnserializedArrayFromJson(
                self::callInternal($app, '/coa/countrynames', 'GET', [$countries])->getContent()
            )
        );
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="list/publications/{country}"),
     *   @SWG\Parameter(
     *     name="country",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="country", regex="^(?<countrycode_regex>[a-z]+)$")
     * )
     * @param Application $app
     * @param string $country
     * @return JsonResponse
     */
    public function listPublicationsFromCountryCode(Application $app, $country) {
        return new JsonResponse(
            ModelHelper::getUnserializedArrayFromJson(
                self::callInternal($app, '/coa/publicationtitles', 'GET', [$country.'/%'])->getContent()
            )
        );
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="list/publications/{publicationcodes}"),
     *   @SWG\Parameter(
     *     name="publicationcodes",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="publicationcodes", regex="^((?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+),){0,9}(?&publicationcode_regex)$")
     * )
     * @param Application $app
     * @param string $publicationcodes
     * @return JsonResponse
     */
    public function listPublicationsFromPublicationCodes(Application $app, $publicationcodes) {
        return new JsonResponse(
            ModelHelper::getUnserializedArrayFromJson(
                self::callInternal($app, '/coa/publicationtitles', 'GET', [$publicationcodes])->getContent()
            )
        );
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="list/issues/{publicationcode}"),
     *   @SWG\Parameter(
     *     name="publicationcode",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="publicationcode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$")
     * )
     * @param Application $app
     * @param string $publicationcode
     * @return JsonResponse
     */
    public function listIssuesFromPublicationCode(Application $app, $publicationcode) {
        return new JsonResponse(
            ModelHelper::getUnserializedArrayFromJson(
                self::callInternal($app, '/coa/issues', 'GET', [$publicationcode])->getContent()
            )
        );
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="list/issuesbycodes/{issuecodes}"),
     *   @SWG\Parameter(
     *     name="issuecodes",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="issuecodes", regex="^((?<issuecode_regex>[a-z]+/[-A-Z0-9 ]+),){0,3}(?&issuecode_regex)$")
     * )
     * @param Application $app
     * @param string $issuecodes
     * @return Response
     */
    public function listIssuesFromIssueCodes(Application $app, $issuecodes) {
        $response = self::callInternal($app, '/coa/issuesbycodes', 'GET', [$issuecodes]);
        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse(ModelHelper::getSimpleArray(
                ModelHelper::getUnserializedArrayFromJson($response->getContent())
            ));
        }
        else {
            return $response;
        }
    }
}
