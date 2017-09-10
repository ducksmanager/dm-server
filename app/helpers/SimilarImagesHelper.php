<?php
namespace DmServer;

use CoverId\Contracts\Dtos\SimilarImagesOutput;
use Exception;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\File\File;

class SimilarImagesHelper {

    /** @var string $mockedResults */
    public static $mockedResults;

    /**
     * @return int
     * @throws Exception
     */
    public static function getIndexedImagesNumber()
    {
        if (!is_null(self::$mockedResults)) {
            $response = self::$mockedResults;
        }
        else {
            // @codeCoverageIgnoreStart
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,
                'http://' . DmServer::$settings['pastec_host'] . ':' . DmServer::$settings['pastec_port'] . '/index/imageIds');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
        }

        $resultArray = json_decode($response, true);
        if (is_null($resultArray)) {
            throw new Exception('Pastec is unreachable');
        }
        if ($resultArray['type'] === 'INDEX_IMAGE_IDS') {
            return count($resultArray['image_ids']);
        }
        else {
            throw new Exception('Invalid return type : '.$resultArray['type']);
        }
    }

    /**
     * @param File $file
     * @param Logger $monolog
     * @return SimilarImagesOutput
     */
    public static function getSimilarImages(File $file, Logger $monolog)
    {
        if (!is_null(self::$mockedResults)) {
            $response = self::$mockedResults;
        }
        else {
            // @codeCoverageIgnoreStart
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://' . DmServer::$settings['pastec_host'] . ':' . DmServer::$settings['pastec_port'] . '/index/searcher');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                file_get_contents($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename()));

            $response = curl_exec($ch);
            $monolog->addInfo('Received response from Pastec: ');
            $monolog->addInfo($response);
            // @codeCoverageIgnoreEnd
        }
        return SimilarImagesOutput::createFromJsonEncodedResult($response);
    }
}