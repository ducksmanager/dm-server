<?php

namespace DmServer\Controllers\Edges;

use DmServer\Controllers\AbstractController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Radebatz\Silex2Swagger\Swagger\Annotations as S2S;
use Swagger\Annotations as SWG;

/**
 * @S2S\Controller(prefix="/edges",
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
     *   @SLX\Request(method="GET", uri="{publicationcode}/{issuenumbers}"),
     *   @SWG\Parameter(
     *     name="publicationcode",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="publicationcode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *   @SWG\Parameter(
     *     name="issuenumbers",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="issuenumbers", regex="^((?P<issuenumber_regex>[-A-Z0-9 ]+),){0,49}(?&issuenumber_regex)$")
     * )
     * @param Application $app
     * @param string $publicationcode
     * @param string $issuenumbers
     * @return Response
     */
    public function getEdges(Application $app, $publicationcode, $issuenumbers) {
        return self::callInternal($app, "/edges/$publicationcode/$issuenumbers");
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="references/{publicationcode}/{issuenumbers}"),
     *   @SWG\Parameter(
     *     name="publicationcode",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="publicationcode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"),
     *   @SWG\Parameter(
     *     name="issuenumbers",
     *     in="path",
     *     required=true
     *   ),
     *	 @SLX\Assert(variable="issuenumbers", regex="^((?P<issuenumber_regex>[-A-Z0-9 ]+),){0,49}(?&issuenumber_regex)$")
     * )
     * @param Application $app
     * @param string $publicationcode
     * @param string $issuenumbers
     * @return Response
     */
    public function getEdgeReferences(Application $app, $publicationcode, $issuenumbers) {
        return self::callInternal($app, "/edges/references/$publicationcode/$issuenumbers");
    }
}
