<?php
namespace DmServer;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait RequestInterceptor
{
    use RequestUtil;

    /**
     * @param Request $request
     * @param Application $app
     * @return Response|void
     */
    static function checkRequestVersionAndUser(Request $request, Application $app)
    {
        if (strpos($request->getPathInfo(), '/status') === 0) {
            return;
        }
        if (is_null(self::getClientVersion($app))) {
            $clientVersion = $request->headers->get('x-dm-version');
            if (is_null($clientVersion)) {
                return new Response('Unspecified client version', Response::HTTP_VERSION_NOT_SUPPORTED);
            } else {
                self::setClientVersion($app, $clientVersion);
            }
        }

        $result = self::authenticateUser($app, $request);
        if ($result instanceof Response) {
            return $result;
        }
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return bool|Response
     */
    static function authenticateUser(Application $app, Request $request) {
        if (
            preg_match('#^/collection/((?!new/?).)+$#', $request->getPathInfo())
            || preg_match('#^/edgecreator/.+$#',           $request->getPathInfo())
        ) {
            $username = $request->headers->get('x-dm-user');
            $password = $request->headers->get('x-dm-pass');
            if (isset($username) && isset($password)) {
                $app['monolog']->addInfo("Authenticating $username...");

                $userCheck = self::callInternal($app, '/user/check', 'GET', [
                    'username' => $username,
                    'password' => $password
                ]);
                if ($userCheck->getStatusCode() !== Response::HTTP_OK) {
                    return new Response('', Response::HTTP_UNAUTHORIZED);
                } else {
                    self::setSessionUser($app, $username, $userCheck->getContent());
                    $app['monolog']->addInfo("$username is logged in");
                    return true;
                }
            }
            else {
                return new Response('', Response::HTTP_UNAUTHORIZED);
            }
        }
    }
}