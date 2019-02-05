<?php
namespace DmitriiKoziuk\yii2Shop\helpers;

use yii\helpers\Inflector;

class UrlHelper
{
    public static function slugFromString(string $string): string
    {
        $string = trim($string);
        $string = Inflector::transliterate($string);
        $string = preg_replace("/[^a-zA-Z0-9\/.\s]/","-", $string);
        $string = preg_replace('/\s{1,}/', '-', $string);
        $string = preg_replace('/[-]{2,}/', '-', $string);
        $string = preg_replace('/[\/]{2,}/', '/', $string);
        $string = trim($string, '-');
        $string = mb_strtolower($string);
        return $string;
    }
}