<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
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
     * @param \stdClass[] $objectArray
     * @return array
     */
    protected static function getSimpleArray($objectArray): array
    {
        return array_map(/**
         * @param GenericReturnObjectInterface $object
         * @return array
         */
            function(GenericReturnObjectInterface $object) {
                return $object->toArray();
            }, $objectArray);
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

    protected function getCurrentUser(): array
    {
        return $this->get('session')->get('user');
    }

    /**
     * @param Response $response
     * @param string $idKey
     * @return mixed
     * @throws \RuntimeException
     */
    protected static function getResponseIdFromServiceResponse(Response $response, string $idKey) {
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \RuntimeException($response->getContent(), $response->getStatusCode());
        }

        return json_decode($response->getContent())->$idKey;
    }
}

interface GenericReturnObjectInterface {
    public function toArray();
}
