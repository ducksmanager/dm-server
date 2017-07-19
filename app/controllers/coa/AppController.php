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

class AppController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/coa/list/countries/{countries}',
            function (Application $app, Request $request, $countries) {
                return new JsonResponse(
                    ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/coa/countrynames', 'GET', empty($countries) ? [] : [$countries])->getContent()
                    )
                );
            }
        )->assert('countries', self::getParamAssertRegex(BaseModel::COUNTRY_CODE_VALIDATION, 50))
         ->value('countries', '');

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
                else return $response;
            }
        )->assert('issuecodes', self::getParamAssertRegex(BaseModel::ISSUE_CODE_VALIDATION, 4));
    }
}
