<?php
namespace DmServer;

class MiscUtil {
    public static function get_random_string($length = 16) {
        $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ";
        $validCharNumber = strlen($validCharacters);
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $result.=$validCharacters[mt_rand(0, $validCharNumber - 1)];
        }

        return $result;
    }
}