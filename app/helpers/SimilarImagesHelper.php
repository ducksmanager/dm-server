<?php
namespace DmServer;

use Coverid\Contracts\Dtos\SimilarImagesOutput;
use Exception;
use InvalidArgumentException;
use Monolog\Logger;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File;

class SimilarImagesHelper {

    /** @var string $mockedResults */
    public static $mockedResults;

    /**
     * @param string $pastecHost
     * @return int
     * @throws \InvalidArgumentException|\RuntimeException
     */
    public static function getIndexedImagesNumber($pastecHost)
    {
        $PASTEC_HOSTS=explode(',', DmServer::$settings['pastec_hosts']);
        if (!in_array($pastecHost, $PASTEC_HOSTS)) {
            throw new InvalidArgumentException("Invalid Pastec host : $pastecHost");
        }
        $PASTEC_PORT=DmServer::$settings['pastec_port'];
        if (!is_null(self::$mockedResults)) {
            $response = self::$mockedResults;
        }
        else {
            // @codeCoverageIgnoreStart
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://$pastecHost:$PASTEC_PORT/index/imageIds");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
        }

        $resultArray = json_decode($response, true);
        if (is_null($resultArray)) {
            throw new RuntimeException('Pastec is unreachable');
        }
        if ($resultArray['type'] === 'INDEX_IMAGE_IDS') {
            return count($resultArray['image_ids']);
        }
        else {
            throw new InvalidArgumentException("Invalid return type : {$resultArray['type']}");
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
