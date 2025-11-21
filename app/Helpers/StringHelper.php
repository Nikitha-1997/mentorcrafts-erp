<?php

namespace App\Helpers;

class StringHelper
{
    public static function abbreviate($text)
    {
        $words = explode(' ', $text);

        // If more than one word → take first letter of each
        if (count($words) > 1) {
            return strtoupper(implode('', array_map(fn($w) => substr($w, 0, 1), $words)));
        }

        // If single word → take first 3 letters
        return strtoupper(substr($text, 0, 3));
    }
}
