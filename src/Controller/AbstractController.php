<?php

namespace App\Controller;

use App\Helper\GenericReturnObjectInterface;
use Doctrine\ORM\EntityManager;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function callService(string $class, string $function, array $parameters = [], array $query = []): Response
    {
        return $this->forward($class.'::'.$function, $parameters, $query);
    }

    protected static function getSerializedArray(array $array): array
    {
        return array_map('serialize', $array);
    }

    /**
     * @param object[] $objectArray
     * @return array
     */
    protected static function getSimpleArray(array $objectArray): array
    {
        return array_map(
            fn(GenericReturnObjectInterface $object) : array => $object->toArray(), $objectArray);
    }

    protected static function getUnserializedArray(array $array): array
    {
        return array_map('unserialize', $array);
    }

    protected static function getUnserializedArrayFromJson(string $json): array
    {
        return self::getUnserializedArray((array)json_decode($json));
    }

    protected function getEm(string $name): EntityManager
    {
        return $this->container->get('doctrine')->getManager($name);
    }

    protected function getSessionUser(): ?array
    {
        return $this->get('session')->get('user');
    }

    protected function getSessionUsername(): ?string
    {
        $sessionUser = $this->getSessionUser();
        return is_null($sessionUser) ? null : $sessionUser['username'];
    }

    /**
     * @param Response $response
     * @param string $idKey
     * @return mixed
     * @throws RuntimeException
     */
    protected static function getResponseIdFromServiceResponse(Response $response, string $idKey) {
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException($response->getContent(), $response->getStatusCode());
        }

        return json_decode($response->getContent())->$idKey;
    }
}
