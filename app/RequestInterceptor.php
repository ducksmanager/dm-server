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
     * @return Response|null
     */
    public static function checkVersion(Request $request, Application $app)
    {
        if (strpos($request->getPathInfo(), '/status') !== 0) {
            if (is_null(self::getClientVersion($app))) {
                $clientVersion = $request->headers->get('x-dm-version');
                if (is_null($clientVersion)) {
                    return new Response('Unspecified client version', Response::HTTP_VERSION_NOT_SUPPORTED);
                } else {
                    self::setClientVersion($app, $clientVersion);
                }
            }
        }
        return null;
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return Response|null
     */
    public static function authenticateUser(Request $request, Application $app) {
        $username = $request->headers->get('x-dm-user');
        $password = $request->headers->get('x-dm-pass');
        if (isset($username, $password)) {
            $app['monolog']->addInfo("Authenticating $username...");

            $userCheck = self::callInternal($app, '/ducksmanager/check', 'GET', [
                'username' => $username,
                'password' => $password
            ]);
            if ($userCheck->getStatusCode() !== Response::HTTP_OK) {
                return new Response('', Response::HTTP_UNAUTHORIZED);
            } else {
                self::setSessionUser($app, $username, $userCheck->getContent());
                $app['monolog']->addInfo("$username is logged in");
            }
        }
        else {
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        return null;
    }
}