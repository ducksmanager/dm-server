<?php
namespace Wtd;

use Silex\Application;
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
    protected static function callInternal(Application $app, $url, $type, $parameters)
    {
        if ($type === 'GET') {
            $subRequest = Request::create('/internal' . $url . '/' . implode('/', array_values($parameters)));
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
}
