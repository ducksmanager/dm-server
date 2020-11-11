<?php
namespace App\Service;

use Cloudinary\Uploader;

class SpriteService {

    public static ?array $mockedResults = null;

    public function upload($file, $options = array()) {
        putenv('CLOUDINARY_URL='.$_ENV['CLOUDINARY_URL']);
        return self::$mockedResults['upload'] ?: Uploader::upload($file, $options);
    }

    public function add_tag($tag, $public_ids = array(), $options = array()) {
        putenv('CLOUDINARY_URL='.$_ENV['CLOUDINARY_URL']);
        return self::$mockedResults['add_tag'] ?: Uploader::add_tag($tag, $public_ids, $options);
    }

    public function generate_sprite($tag, $options = array()) {
        putenv('CLOUDINARY_URL='.$_ENV['CLOUDINARY_URL']);
        return self::$mockedResults['generate_sprite'][$tag] ?: Uploader::generate_sprite($tag, $options);
    }
}
