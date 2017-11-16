<?php

namespace DmServer\Controllers\User;

use DmServer\Controllers\AbstractController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Radebatz\Silex2Swagger\Swagger\Annotations as S2S;
use Swagger\Annotations as SWG;

/**
 * @S2S\Controller(prefix="/user/sale",
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
     *   @SLX\Request(method="POST", uri="{otheruser}"),
     *   @SWG\Parameter(
     *     name="otheruser",
     *     in="path",
     *     required=true
     *   )
     * )
     * @codeCoverageIgnore
     * @param Application $app
     * @param string $otheruser
     * @return Response
     */
    public function sellToUser(Application $app, $otheruser) {
        if (self::callInternal($app, '/ducksmanager/exists', 'GET', [$otheruser])->getStatusCode() === Response::HTTP_NO_CONTENT) {
            return new Response(self::$translator->trans('UTILISATEUR_INVALIDE'), Response::HTTP_BAD_REQUEST);
        }

        return self::callInternal($app, "/user/sale/$otheruser", 'POST');
    }

    /**
     * @SLX\Route(
     *   @SLX\Request(method="GET", uri="{otheruser}/{date}"),
     *   @SWG\Parameter(
     *     name="otheruser",
     *     in="path",
     *     required=true
     *   )
     * )
     * @codeCoverageIgnore
     * @param Application $app
     * @param string $otheruser
     * @param string $date
     * @return Response
     */
    public function getSaleToUserAtDate(Application $app, $otheruser, $date) {
        return self::callInternal($app, "/user/sale/$otheruser/$date", 'GET');
    }
}
