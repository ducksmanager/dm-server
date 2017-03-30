<?php
namespace DmServer\Controllers;

use DmServer\DmServer;
use DmServer\ModelHelper;
use Silex\Application;
use Silex\Application\TranslationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractController
{
    /** @var $translator TranslationTrait */
    static $translator;

    static function initTranslation($app) {
        self::$translator = $app['translator'];
    }

    protected static function getSerializer() {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return new Serializer($normalizers, $encoders);
    }

    private static function callInternalGetRequest(Application $app, $url, $parameters = []) {
        $subRequest = Request::create('/internal' . $url . (count($parameters) === 0 ? '' : '/' . implode('/', array_values($parameters))));

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

    /**
     * @param Application $app
     * @param string $username
     * @param $userId
     */
    protected static function setSessionUser(Application $app, $username, $userId) {
        $app['session']->set('user', ['username' => $username, 'id' => $userId]);
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

    public function authenticateUser(Application $app, Request $request) {
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
                    $this->setSessionUser($app, $username, $userCheck->getContent());
                    $app['monolog']->addInfo("$username is logged in");
                    return true;
                }
            }
            else {
                return new Response('', Response::HTTP_UNAUTHORIZED);
            }
        }
    }

    /**
     * @param Response $response
     * @param string $idKey
     * @return int
     * @throws UnexpectedInternalCallResponseException
     */
    protected static function getResponseIdFromServiceResponse($response, $idKey) {
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new UnexpectedInternalCallResponseException($response->getContent(), $response->getStatusCode());
        }

        return json_decode($response->getContent())->$idKey;
    }

    /**
     * @param Application $app
     * @param callable $function
     * @return mixed|Response
     * @throws \Exception
     */
    protected static function wrapInternalService($app, $function) {
        throw new \Exception('Should not be called directly');
    }

    /**
     * @param Application $app
     * @param string $integratedEm
     * @param callable $function
     * @return mixed|Response
     */
    protected static function return500ErrorOnException($app, $integratedEm, $function) {
        try {
            return call_user_func($function, DmServer::getEntityManager($integratedEm));
        }
        catch (\Exception $e) {
            if (isset($app['monolog'])) {
                $app['monolog']->addError($e->getMessage());
            }
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected static function getParamAssertRegex($baseRegex, $maxOccurrences = 1) {
        if ($maxOccurrences === 1) {
            return '^'.$baseRegex.'$';
        }
        return '^('.$baseRegex.',){0,'.($maxOccurrences-1).'}'.$baseRegex.'$';
    }
}
