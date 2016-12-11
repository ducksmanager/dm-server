<?php
namespace Wtd;

use Silex\Application;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Silex\Application\TranslationTrait;

abstract class AppController
{
    /**
     * @param Application $app
     * @param string $url
     * @param string $type
     * @param array $parameters
     * @return Response
     */
    protected static function callInternal(Application $app, $url, $type, $parameters = [])
    {
        if ($type === 'GET') {
            $subRequest = Request::create('/internal' . $url . (count($parameters) === 0 ? '' : '/' . implode('/', array_values($parameters))));
        }
        else {
            $subRequest = Request::create('/internal' . $url, $type, $parameters);
        }
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }

    /**
     * @param Application $app
     * @param string $string
     * @return string
     */
    protected static function translate(Application $app, $string) {
        /** @var TranslationTrait $translator */
        $translator = $app['translator'];

        return $translator->trans($string);
    }

    /**
     * @param Application $app
     * @param string $username
     * @param $userId
     */
    protected static function setSessionUser(Application $app, $username, $userId) {
        $app['session']->set('user', array('username' => $username, 'id' => $userId));
    }

    /**
     * @param Application $app
     * @return string
     */
    public static function getSessionUser(Application $app) {
        return $app['session']->get('user');
    }

    /**
     * @param Application $app
     * @param string $clientVersion
     */
    protected static function setClientVersion(Application $app, $clientVersion) {
        $app['session']->set('clientVersion', $clientVersion);
    }

    /**
     * @param Application $app
     * @return string
     */
    public static function getClientVersion(Application $app) {
        return $app['session']->get('clientVersion');
    }

    public function authenticateUser(Application $app, $request) {
        if (preg_match('#^/collection/((?!new/?).)+$#', $request->getPathInfo())) {
            $authHeader = $request->headers->get('authorization');
            list($type, $base64Auth) = explode(' ', $authHeader);
            $base64AuthParts = explode(':', base64_decode($base64Auth));
            if (count($base64AuthParts) === 2) {
                list($username, $password) = $base64AuthParts;

                $userCheck = self::callInternal($app, '/user/check', 'GET', [
                    'username' => $username,
                    'password' => $password
                ]);
                if ($userCheck->getStatusCode() !== Response::HTTP_OK) {
                    return $userCheck;
                } else {
                    $this->setSessionUser($app, $username, $userCheck->getContent());
                    return true;
                }
            }
            else {
                return new Response('', Response::HTTP_UNAUTHORIZED);
            }
        }
    }

    protected static function return500ErrorOnException($function) {
        try {
            return call_user_func($function);
        }
        catch (Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
