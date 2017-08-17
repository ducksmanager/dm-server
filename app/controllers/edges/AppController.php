<?php

namespace DmServer\Controllers\Edges;

use Coa\Models\BaseModel;
use DmServer\Controllers\AbstractController;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class AppController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/edges/{publicationcode}/{issuenumbers}',
            function (Application $app, Request $request, $publicationcode, $issuenumbers) {
                return self::callInternal($app, "/edges/$publicationcode/$issuenumbers", 'GET');
            }
        )
            ->assert('publicationcode', self::getParamAssertRegex(BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('issuenumbers', self::getParamAssertRegex(BaseModel::ISSUE_NUMBER_VALIDATION, 50));

        $routing->get(
            '/edges/references/{publicationcode}/{issuenumbers}',
            function (Application $app, Request $request, $publicationcode, $issuenumbers) {
                return self::callInternal($app, "/edges/references/$publicationcode/$issuenumbers", 'GET');
            }
        )
            ->assert('publicationcode', self::getParamAssertRegex(BaseModel::PUBLICATION_CODE_VALIDATION))
            ->assert('issuenumbers', self::getParamAssertRegex(BaseModel::ISSUE_NUMBER_VALIDATION, 50));
    }
}
