<?php
namespace Wtd;

class ModelHelper {
    /**
     * @param array $array
     * @return array
     */
    static function getSerializedArray($array) {
        return array_map(function($object) {
            return serialize($object);
        }, $array);
    }

    /**
     * @param array $array
     * @return array
     */
    static function getUnserializedArray($array) {
        return array_map(function($object) {
            return unserialize($object);
        }, $array);
    }

    /**
     * @param string $json
     * @return array
     */
    static function getUnserializedArrayFromJson($json) {
        return self::getUnserializedArray((array)json_decode($json));
    }
}