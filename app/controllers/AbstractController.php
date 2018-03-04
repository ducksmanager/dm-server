<?php
namespace DmServer\Controllers;

use DmServer\DmServer;
use DmServer\RequestUtil;
use Edgecreator\Models\TranchesEnCoursModeles;
use Silex\Application;
use Silex\Application\TranslationTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     basePath="/dm-server",
 *     host="163.172.211.199:8001",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="DM server API"
 *     )
 * )
 */

abstract class AbstractController
{
    use RequestUtil;

    /** @var $translator TranslationTrait */
    public static $translator;

    public static function initTranslation($app) {
        self::$translator = $app['translator'];
    }

    protected static function getSerializer() {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function ($object) {
            if (get_class($object) === TranchesEnCoursModeles::class) {
                /** @var TranchesEnCoursModeles $object */
                return $object->getId();
            }
            return null;
        });

        return new Serializer([$normalizer], [$encoder]);
    }

    /**
     * @param Response $response
     * @param string $idKey
     * @return mixed
     * @throws UnexpectedInternalCallResponseException
     */
    protected static function getResponseIdFromServiceResponse(Response $response, $idKey) {
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
            return $function(DmServer::getEntityManager($integratedEm));
        }
        catch (\Exception $e) {
            if (isset($app['monolog'])) {
                $app['monolog']->addError($e->getMessage());
            }
            return new Response($e->getMessage(), $e->getCode() === 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : $e->getCode());
        }
    }
}
