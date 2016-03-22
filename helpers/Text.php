<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 22.04.2015
 * Time: 20:54
 */

namespace ra\admin\helpers;

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
        $text = trim(preg_replace('/\s(\S{1,' . ($minLength - 1) . '})\s/', ' ', $text), '-');
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

    public static function translate($text, $type = 1)
    {
        $url = '';
        $translate = \Yii::$app->translation->translate(\Yii::$app->language, \Yii::$app->sourceLanguage, $text);
        if (isset($translate['code']) && $translate['code'] == 200) {
            $translation = current($translate['text']);
            $translation = preg_replace('#[^\w\d]#', ' ', strtolower($translation));
            $translation = preg_split('#\s+#', trim($translation));
            $translation = array_diff($translation, ['the', 'a', 'an']);
            if ($type) foreach ($translation as $word)
                $url .= $word == reset($translation) ? $word : ucfirst($word);
            else $url = implode('-', $translation);
        }
        return $url;
    }

    public static function cleverStrip($text, $length, $split = ['.', '?', '!', ',', ' '], $proportional = 2 / 3)
    {
        $stripText = mb_substr($text, 0, $length, 'utf8');
        foreach ((array)$split as $value) {
            $last = strrpos($row['about'], $value);
            if (!$result && $last > $length * $proportional) {
                $result = mb_substr($text, 0, $last + 1, 'utf8');
                break;
            }
        }

        return trim($result);
    }
}
