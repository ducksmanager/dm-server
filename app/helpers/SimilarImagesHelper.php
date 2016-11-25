<?php
namespace Wtd;

use Symfony\Component\HttpFoundation\File\File;

class SimilarImagesHelper {
    static $pastecPort = 4212;

    public static function getInstance() {
        return new SimilarImagesHelper();
    }

    /**
     * @param File $file
     * @return string
     */
    public static function getSimilarImages($file)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:' . self::$pastecPort . '/index/searcher');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file->getPath()));

        $response = curl_exec($ch);
        return $response;
    }

    /**
     * @param File $file
     * @return string
     */
    public static function getSimilarImagesMocked($file)
    {
        return json_encode("{\"bounding_rects\":[{\"height\":846,\"width\":625,\"x\":67,\"y\":44}],\"image_ids\":[2],\"scores\":[58.0],\"tags\":[\"\"],\"type\":\"SEARCH_RESULTS\"}");
    }
}