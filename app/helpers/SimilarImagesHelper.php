<?php
namespace DmServer;

use CoverId\Contracts\Dtos\SimilarImagesOutput;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\File\File;

class SimilarImagesHelper {

    /** @var string $mockedResults */
    public static $mockedResults = null;

    /**
     * @param File $file
     * @param Logger $monolog
     * @return SimilarImagesOutput
     */
    public static function getSimilarImages($file, $monolog)
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