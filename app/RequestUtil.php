<?php
namespace DmServer;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

trait RequestUtil
{
    /**
     * @param Application $app
     * @param string $username
     * @param $userId
     */
    public static function setSessionUser(Application $app, $username, $userId) {
        $app['session']->set('user', ['username' => $username, 'id' => $userId]);
    }

    /**
     * @param Application $app
     * @return array
     */
    public static function getSessionUser(Application $app) {
        return $app['session']->get('user');
    }

    /**
     * @param Application $app
     * @param string $clientVersion
     */
    public static function setClientVersion(Application $app, $clientVersion) {
        $app['session']->set('clientVersion', $clientVersion);
    }

    /**
     * @param Application $app
     * @return string
     */
    public static function getClientVersion(Application $app) {
        return $app['session']->get('clientVersion');
    }

    public static function buildUrl($url, $getParameters = []) {
        return $url . (count($getParameters) === 0 ? '' : '/' . implode('/', array_values($getParameters)));
    }

    public static function callInternalGetRequest(Application $app, $url, $parameters = []) {
        $subRequest = Request::create(self::buildUrl('/internal'.$url, $parameters));
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }

    /**
     * @param Application $app
     * @param string $url
     * @param string $type
     * @param array $parameters
     * @param int $chunkSize
     * @return Response|array
     */
    public static function callInternal(Application $app, $url, $type = 'GET', $parameters = [], $chunkSize = 0) {
        if ($chunkSize > 1) {
            if (count($parameters) > 1) {
                return new Response('Attempt to call callInternal with chunkSize > 1 and more than one parameter', Response::HTTP_INTERNAL_SERVER_ERROR);
            } elseif (count($parameters) === 1) {
                if ($type !== 'GET') {
                    return new Response('Attempt to call callInternal with chunkSize > 1 and non-GET method', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                $parameterListChunks = array_chunk($parameters[0], $chunkSize);
                $results = [];
                foreach ($parameterListChunks as $parameterListChunk) {
                    $results = array_merge(
                        $results,
                        ModelHelper::getUnserializedArrayFromJson(
                            self::callInternalGetRequest(
                                $app, $url, [implode(',', $parameterListChunk)]
                            )->getContent()
                        )
                    );
                }

                return $results;
            }
        }

        if ($type === 'GET') {
            return self::callInternalGetRequest($app, $url, $parameters);
        }
        else {
            $subRequest = Request::create('/internal' . $url, $type, $parameters);
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
        }
    }
}