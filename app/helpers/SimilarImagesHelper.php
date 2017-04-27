<?php
namespace DmServer;

use CoverId\Contracts\Dtos\SimilarImagesOutput;
use Symfony\Component\HttpFoundation\File\File;

class SimilarImagesHelper {
    static $pastecHost = 'pastec';
    static $pastecPort = 4212;

    /** @var string $mockedResults */
    public static $mockedResults = null;

    /**
     * @param File $file
     * @return SimilarImagesOutput
     */
    public static function getSimilarImages($file)
    {
        if (!is_null(self::$mockedResults)) {
            $response = self::$mockedResults;
        }
        else {
            // @codeCoverageIgnoreStart
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://' . self::$pastecHost . ':' . self::$pastecPort . '/index/searcher');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                file_get_contents($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename()));

            $response = curl_exec($ch);
            // @codeCoverageIgnoreEnd
        }
        return SimilarImagesOutput::createFromJsonEncodedResult($response);
    }
}