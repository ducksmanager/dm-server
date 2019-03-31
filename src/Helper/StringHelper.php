<?php
namespace App\Helper;

class StringHelper {
    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public static function getRandomString($length = 16): string
    {
        $validCharacters = 'abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ';
        $validCharNumber = strlen($validCharacters);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result.=$validCharacters[random_int(0, $validCharNumber - 1)];
        }

        return $result;
    }
}
