<?php
namespace DmServer\Controllers;

use DmServer\DmServer;
use DmServer\RequestUtil;
use Silex\Application;
use Silex\Application\TranslationTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractController
{
    use RequestUtil;

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
     * @param string $integratedEm
     * @param callable $function
     * @return mixed|Response
     */
    protected static function returnErrorOnException($app, $integratedEm, $function) {
        try {
            return call_user_func($function, DmServer::getEntityManager($integratedEm));
        }
        catch (\Exception $e) {
            if (isset($app['monolog'])) {
                $app['monolog']->addError($e->getMessage());
            }
            return new Response($e->getMessage(), $e->getCode() === 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : $e->getCode());
        }
    }

    protected static function getParamAssertRegex($baseRegex, $maxOccurrences = 1) {
        if ($maxOccurrences === 1) {
            return '^'.$baseRegex.'$';
        }
        return '^('.$baseRegex.',){0,'.($maxOccurrences-1).'}'.$baseRegex.'$';
    }
}
