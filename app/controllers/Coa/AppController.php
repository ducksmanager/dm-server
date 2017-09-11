<?php

namespace DmServer\Controllers\Coa;

use Coa\Models\BaseModel;
use DmServer\Controllers\AbstractController;
use DmServer\ModelHelper;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Swagger\Annotations as SWG;

/**
 * @SLX\Controller(prefix="/coa")
 * @SLX\Before("DmServer\RequestInterceptor::checkRequestVersionAndUser")
 *
 */
class AppController extends AbstractController
{
    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="list/countries"),
     *
     *   @SWG\Parameter(
     *     name="x-dm-server",
     *     in="header",
     *     required=true
     *   ),
     *
     *   @SWG\Response(response=200),
     *   @SWG\Response(response="default", description="Error")
     * )
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     */
    public function listCountries(Application $app, Request $request) {
        return new JsonResponse(
            ModelHelper::getUnserializedArrayFromJson(
                self::callInternal($app, '/coa/countrynames', 'GET', [])->getContent()
            )
        );
    }
    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="list/countries/{countries}"),
     *	 @SLX\Assert(variable="countries", regex="^((?<countrycode>[a-z]+),){0,9}(?&countrycode)$"),
     *
     *   @SWG\Parameter(
     *     name="x-dm-server",
     *     in="header",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="countries",
     *     in="path",
     *     required=true
     *   ),
     *
     *   @SWG\Response(response=200),
     *   @SWG\Response(response="default", description="Error")
     * )
     * @param Application $app
     * @param Request $request
     * @param string $countries
     * @return JsonResponse
     */
    public function listCountriesFromCodes(Application $app, Request $request, $countries) {
        return new JsonResponse(
            ModelHelper::getUnserializedArrayFromJson(
                self::callInternal($app, '/coa/countrynames', 'GET', [$countries])->getContent()
            )
        );
    }

    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/coa/list/publications/{country}',
            function (Application $app, Request $request, $country) {
                return new JsonResponse(
                    ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/coa/publicationtitles', 'GET', [$country.'/%'])->getContent()
                    )
                );
            }
        )->assert('country', self::getParamAssertRegex(BaseModel::COUNTRY_CODE_VALIDATION));

        $routing->get(
            '/coa/list/publications/{publicationcodes}',
            function (Application $app, Request $request, $publicationcodes) {
                return new JsonResponse(
                    ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/coa/publicationtitles', 'GET', [$publicationcodes])->getContent()
                    )
                );
            }
        )->assert('publicationcodes', self::getParamAssertRegex(BaseModel::PUBLICATION_CODE_VALIDATION, 10));

        $routing->get(
            '/coa/list/issues/{publicationcode}',
            function (Application $app, Request $request, $publicationcode) {
                return new JsonResponse(
                    ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/coa/issues', 'GET', [$publicationcode])->getContent()
                    )
                );
            }
        )->assert('publicationcode', self::getParamAssertRegex(BaseModel::PUBLICATION_CODE_VALIDATION));

        $routing->get(
            '/coa/list/issuesbycodes/{issuecodes}',
            function (Application $app, Request $request, $issuecodes) {
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
        )->assert('issuecodes', self::getParamAssertRegex(BaseModel::ISSUE_CODE_VALIDATION, 4));
    }
}
