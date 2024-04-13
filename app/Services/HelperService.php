<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class HelperService
{
    private static array $forbiddenCharacters = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '='];
    private static string $replacementCharacter = '_';

    public static function sanitizeFileName(string $fileName): string
    {
        foreach (self::$forbiddenCharacters as $char) {
            $fileName = str_replace($char, self::$replacementCharacter, $fileName);
        }

        return Str::of($fileName)->replace(' ', self::$replacementCharacter);
    }

    public static function encryptAndStripTags($input): string
    {
        return Crypt::encryptString(strip_tags($input));
    }

    public static function convertNullStringToNull($value)
    {
        return strtolower(trim($value)) === "null" ? null : $value;
    }
}
