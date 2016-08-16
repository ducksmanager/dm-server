<?php

namespace Wtd;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CoaListController extends AppController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/coa/list/countries',
            function (Application $app, Request $request) {
                return new JsonResponse(
                    ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/coa/countrynames', 'GET', [])->getContent()
                    )
                );
            }
        );

        $routing->get(
            '/coa/list/publications/{country}',
            function (Application $app, Request $request, $country) {
                return new JsonResponse(
                    ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/coa/publicationtitles', 'GET', [$country.'/%'])->getContent()
                    )
                );
            }
        )->assert('country', '[a-z]+');

        $routing->get(
            '/coa/list/issues/{publicationcode}',
            function (Application $app, Request $request, $publicationcode) {
                return new JsonResponse(
                    ModelHelper::getUnserializedArrayFromJson(
                        self::callInternal($app, '/coa/issues', 'GET', [$publicationcode])->getContent()
                    )
                );
            }
        )->assert('publicationcode', '^[a-z]+/[-A-Z0-9]+$');
    }
}
