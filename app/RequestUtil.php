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

    /**
     * @param Application $app
     * @param string      $url
     * @param array       $parameters
     * @return Response
     * @throws \InvalidArgumentException
     */
    public static function callInternalGetRequest(Application $app, $url, $parameters = []) {
        $subRequest = Request::create(self::buildUrl('/internal'.$url, $parameters));
        try {
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
        } catch (\Exception $e) {
            return new Response("Failed to call internal method GET {$subRequest->getUri()}", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Application $app
     * @param string      $url
     * @param string      $type
     * @param array       $parameters
     * @param int         $chunkSize
     * @return Response|array
     * @throws \InvalidArgumentException
     */
    public static function callInternal(Application $app, $url, $type = 'GET', $parameters = [], $chunkSize = 0) {
        if ($chunkSize > 1) {
            if (count($parameters) > 1) {
                return new Response('Attempt to call callInternal with chunkSize > 1 and more than one parameter', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if (count($parameters) === 1) {
                if ($type !== 'GET') {
                    return new Response('Attempt to call callInternal with chunkSize > 1 and non-GET method', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                $parameterListChunks = array_chunk($parameters[0], $chunkSize);
                $results = array_merge(...array_map(function($parameterListChunk) use ($url, $app) {
                    return ModelHelper::getUnserializedArrayFromJson(
                        self::callInternalGetRequest(
                            $app, $url, [implode(',', $parameterListChunk)]
                        )->getContent()
                    );
                }, $parameterListChunks));

                return $results;
            }
        }

        if ($type === 'GET') {
            return self::callInternalGetRequest($app, $url, $parameters);
        }

        $subRequest = Request::create('/internal' . $url, $type, $parameters);
        try {
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
        } catch (\Exception $e) {
            return new Response("Failed to call internal method $type $url", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
