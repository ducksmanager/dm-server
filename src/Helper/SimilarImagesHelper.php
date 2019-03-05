<?php
namespace App\Helper;

use App\EntityTransform\SimilarImagesResult;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File;

class SimilarImagesHelper {

  /** @var string $mockedResults */
  public static $mockedResults;

  /** @var string $sampleCover */
  public static $sampleCover = 'https://outducks.org/au/bp/001/au_bp_001a_001.jpg';

  /**
   * @param string $pastecHost
   * @return string
   * @throws \InvalidArgumentException
   */
  private static function getPastecUrl($pastecHost = null): string
  {
      $PASTEC_HOSTS=explode(',', $_ENV['PASTEC_HOSTS']);
      if (is_null($pastecHost)) {
          $pastecHost = $PASTEC_HOSTS[0];
      }
      else if (!in_array($pastecHost, $PASTEC_HOSTS, true)) {
          throw new InvalidArgumentException("Invalid Pastec host : $pastecHost");
      }
      $PASTEC_PORT=$_ENV['PASTEC_PORT'];

      return "http://$pastecHost:$PASTEC_PORT/index";
  }

  /**
   * @param string $pastecHost
   * @return int
   * @throws \InvalidArgumentException|\RuntimeException
   */
  public static function getIndexedImagesNumber($pastecHost): ?int
  {
      $pastecUrl = self::getPastecUrl($pastecHost);
      if (!is_null(self::$mockedResults)) {
          $response = self::$mockedResults;
      }
      else {
          // @codeCoverageIgnoreStart
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,"$pastecUrl/imageIds");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

          $response = curl_exec($ch);
          // @codeCoverageIgnoreEnd
      }

      $resultArray = json_decode($response, true);
      if (is_null($resultArray)) {
          throw new RuntimeException('Pastec is unreachable');
      }
      if ($resultArray['type'] !== 'INDEX_IMAGE_IDS') {
          throw new InvalidArgumentException("Invalid return type : {$resultArray['type']}");
      }

      return count($resultArray['image_ids']);
  }

  /**
   * @param File $file
   * @param LoggerInterface $logger
   * @param string $pastecHost
   * @return SimilarImagesResult
   */
  public static function getSimilarImages(File $file, LoggerInterface $logger, $pastecHost = 'pastec'): SimilarImagesResult
  {
      $pastecUrl = self::getPastecUrl($pastecHost);
      if (!is_null(self::$mockedResults)) {
          $response = self::$mockedResults;
      }
      else {
          // @codeCoverageIgnoreStart
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, "$pastecUrl/searcher");
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS,
              file_get_contents($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename()));

          $response = curl_exec($ch);
          $logger->info('Received response from Pastec: ');
          $logger->info($response);
          // @codeCoverageIgnoreEnd
      }
      return SimilarImagesResult::createFromJsonEncodedResult($response);
  }
}
