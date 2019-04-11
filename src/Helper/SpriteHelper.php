<?php
namespace App\Helper;

use App\EntityTransform\SimilarImagesResult;
use Cloudinary\Uploader;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File;

class SpriteHelper {

  /** @var array $mockedResults */
  public static $mockedResults;

    public static function upload($file, $options = array()) {
        return self::$mockedResults['upload'] ?: Uploader::upload($file, $options);
    }

    public static function add_tag($tag, $public_ids = array(), $options = array()) {
        return self::$mockedResults['add_tag'] ?: Uploader::add_tag($tag, $public_ids, $options);
    }

    public static function generate_sprite($tag, $options = array()) {
        return self::$mockedResults['generate_sprite'][$tag] ?: Uploader::generate_sprite($tag, $options);
    }
}
