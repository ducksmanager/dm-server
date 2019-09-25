<?php
namespace App\Service;

use Cloudinary\Uploader;

class SpriteService {

  /** @var array $mockedResults */
  public static $mockedResults;

    public function upload($file, $options = array()) {
        return self::$mockedResults['upload'] ?: Uploader::upload($file, $options);
    }

    public function add_tag($tag, $public_ids = array(), $options = array()) {
        return self::$mockedResults['add_tag'] ?: Uploader::add_tag($tag, $public_ids, $options);
    }

    public function generate_sprite($tag, $options = array()) {
        return self::$mockedResults['generate_sprite'][$tag] ?: Uploader::generate_sprite($tag, $options);
    }
}
