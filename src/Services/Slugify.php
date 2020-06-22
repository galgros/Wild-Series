<?php


namespace App\Services;


class Slugify
{
    public function generate(string $input): string
    {
        $input = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $input);
        $input = preg_replace(
            '/[\.\(\)\[\]\"\'\-,;:\/!\?]+/',
            '', trim($input)
        );
        return preg_replace(
        '/ +/',
        '-', strtolower($input)
    );
    }
}