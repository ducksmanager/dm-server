<?php
namespace DmServer;

use Generic\Contracts\Results\GenericReturnObjectInterface;

class ModelHelper {
    /**
     * @param array $array
     * @return array
     */
    public static function getSerializedArray($array) {
        return array_map(function($object) {
            return serialize($object);
        }, $array);
    }

    /**
     * @param \stdClass[] $objectArray
     * @return array
     */
    public static function getSimpleArray($objectArray) {
        return array_map(/**
         * @param \stdClass $object
         * @return mixed
         */
            function($object) {
            /** @var GenericReturnObjectInterface $object */
            return $object->toArray();
        }, $objectArray);
    }

    /**
     * @param array $array
     * @return array
     */
    public static function getUnserializedArray($array) {
        return array_map(function($object) {
            return unserialize($object);
        }, $array);
    }

    /**
     * @param string $json
     * @return array
     */
    public static function getUnserializedArrayFromJson($json) {
        return self::getUnserializedArray((array)json_decode($json));
    }
}