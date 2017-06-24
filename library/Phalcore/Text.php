<?php

namespace Phalcore;

class Text
{
    public static function slugify(string $str, string $replacement = '-'): string
    {
        if (!self::isMultiByte($str)) {
            $str = @iconv('UTF8', 'ASCII//TRANSLIT', $str);
            $str = trim(preg_replace("/[^a-z\d\w]/", $replacement, strtolower($str)), $replacement);
        } else {
            $str = trim(preg_replace("/[^\pL\pM\pN]+/u", $replacement, mb_strtolower($str)), $replacement);
        }

        $pattern = $replacement == '.' ? "\.\.*" : $replacement . $replacement . "*";
        $str = preg_replace("/" . $pattern . "/u", $replacement, $str);

        return $str;
    }

    public static function isMultiByte(string $str): bool
    {
        return strlen($str) != mb_strlen($str);
    }

}

