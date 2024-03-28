<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class HelperService
{
    private static $forbiddenCharacters = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '='];
    private static $replacementCharacter = '_';

    public static function sanitizeFileName(string $fileName): string
    {
        foreach (self::$forbiddenCharacters as $char) {
            $fileName = str_replace($char, self::$replacementCharacter, $fileName);
        }

        return Str::of($fileName)->replace(' ', self::$replacementCharacter);
    }

    public static function encryptAndStripTags($input)
    {
        return Crypt::encryptString(strip_tags($input));
    }
}
