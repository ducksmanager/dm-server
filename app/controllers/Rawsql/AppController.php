<?php

namespace DmServer\Controllers\Rawsql;

use DmServer\Controllers\AbstractController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Radebatz\Silex2Swagger\Swagger\Annotations as S2S;
use Swagger\Annotations as SWG;

/**
 * @S2S\Controller(
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
     *   @SLX\Request(method="POST", uri="/rawsql"),
     *   @SWG\Parameter(
     *     name="query",
     *     in="query",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     name="query",
     *     in="query",
     *     required=true
     *   )
     * )
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function runQuery(Application $app, Request $request) {
        return self::callInternal($app, '/rawsql', 'POST', [
            'query' => $request->request->get('query'),
            'db' => $request->request->get('db')
        ]);
    }
}
