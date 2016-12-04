<?php
namespace Wtd;

use Symfony\Component\HttpFoundation\File\File;

class SimilarImagesHelper {
    static $pastecHost = 'pastec';
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
        curl_setopt($ch, CURLOPT_URL, 'http://' . self::$pastecHost . ':' . self::$pastecPort . '/index/searcher');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file->getPath().DIRECTORY_SEPARATOR.$file->getFilename()));

        $response = curl_exec($ch);
        return json_decode($response, true);
    }

    /**
     * @param File $file
     * @return string
     */
    public static function getSimilarImagesMocked($file)
    {
        return array(
            "bounding_rects" => array(
                "height" => 846,
                "width"  => 625,
                "x" => 67,
                "y" => 44
            ),
            "image_ids" => array(2),
            "scores" => array(58.0),
            "tags" => array(''),
            "type" => "SEARCH_RESULTS"
        );
    }
}