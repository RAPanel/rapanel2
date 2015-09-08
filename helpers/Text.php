<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 22.04.2015
 * Time: 20:54
 */

namespace app\admin\helpers;

class Text
{
    /**
     * @param string $text
     * @param string $separate
     * @return array|mixed|string
     */
    public static function search($text, $separate = null, $minLength = 3)
    {
        $text = mb_substr($text, 0, 64, 'utf-8');
        $text = preg_replace('/[^\w\x7F-\xFF\s]/', ' ', $text);
        $text = str_replace(array('+', '_'), ' ', $text);
        $text = trim(preg_replace('/\s(\S{1,'.($minLength-1).'})\s/', ' ', $text), '-');
        $text = preg_replace('/\s+/', ' ', $text);
        $text = explode(' ', $text);
        foreach ($text as $key => $row) foreach (explode('|', 'у|ы|а|о|я|е|и|ь|ие|ия|ем|им|ию|ий|ии|ой|ов|ам|их|ый|ых|ая|ай|ае|ую|ым|ое') as $s)
            if (mb_strlen($row) > mb_strlen($s) + 2 && mb_substr($row, -mb_strlen($s)) == $s) $text[$key] = mb_substr($row, 0, mb_strlen($row) - mb_strlen($s));
            elseif (mb_strlen($row) >= $minLength) $text[$key] = $row;

        if (is_null($separate)) return $text;
        $text = implode($separate, $text);
        $text = $separate . $text . $separate;
        return $text;
    }

    public static function numeric($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        return sprintf($titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]], $number);
    }
}
