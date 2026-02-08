<?php

namespace App\Helpers;

class Terbilang
{
    public static function convert($number)
    {
        $number = abs((int) $number);

        $words = [
            "", "Satu", "Dua", "Tiga", "Empat", "Lima",
            "Enam", "Tujuh", "Delapan", "Sembilan",
            "Sepuluh", "Sebelas"
        ];

        if ($number < 12) {
            return $words[$number];
        } elseif ($number < 20) {
            return self::convert($number - 10) . " Belas";
        } elseif ($number < 100) {
            return self::convert(intval($number / 10)) . " Puluh " . self::convert($number % 10);
        } elseif ($number < 200) {
            return "Seratus " . self::convert($number - 100);
        } elseif ($number < 1000) {
            return self::convert(intval($number / 100)) . " Ratus " . self::convert($number % 100);
        } elseif ($number < 2000) {
            return "Seribu " . self::convert($number - 1000);
        } elseif ($number < 1000000) {
            return self::convert(intval($number / 1000)) . " Ribu " . self::convert($number % 1000);
        } elseif ($number < 1000000000) {
            return self::convert(intval($number / 1000000)) . " Juta " . self::convert($number % 1000000);
        }

        return "terlalu besar";
    }
}
