<?php
namespace DmServer;


use Generic\Contracts\Results\GenericReturnObjectInterface;

class ModelHelper {
    /**
     * @param array $array
     * @return array
     */
    public static function getSerializedArray($array) {
        return array_map('serialize', $array);
    }

    /**
     * @param \stdClass[] $objectArray
     * @return array
     */
    public static function getSimpleArray($objectArray) {
        return array_map(/**
         * @param GenericReturnObjectInterface $object
         * @return array
         */
            function(GenericReturnObjectInterface $object) {
            return $object->toArray();
        }, $objectArray);
    }

    /**
     * @param array $array
     * @return array
     */
    public static function getUnserializedArray($array) {
        return array_map('unserialize', $array);
    }

    /**
     * @param string $json
     * @return array
     */
    public static function getUnserializedArrayFromJson($json) {
        return self::getUnserializedArray((array)json_decode($json));
    }
}
