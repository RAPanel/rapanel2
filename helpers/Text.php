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

    /**
     * @param $number
     * Количество для скланения
     * @param $titles
     * массив в виде <code>['одна', 'две', 'пять']</code>
     * для подстановки цифры используется символ <i>%</i>
     * @return string
     */
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
        $result = false;
        $stripText = mb_substr(strip_tags($text), 0, $length, 'utf8');
        foreach ((array)$split as $value) {
            $last = strrpos($stripText, $value);
            if (!$result && $last > $length * $proportional) {
                $result = mb_substr($stripText, 0, $last + 1, 'utf8');
                break;
            }
        }

        return trim($result);
    }

    static function num2str($inn, $stripkop = false)
    {
        $nol = 'ноль';
        $str[100] = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $str[11] = array('', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать', 'двадцать');
        $str[10] = array('', 'десять', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $sex = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'), // m
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять') // f
        );

        $forms = array(
            array('копейка', 'копейки', 'копеек', 1), // 10^-2
            array('рубль', 'рубля', 'рублей', 0), // 10^ 0
            array('тысяча', 'тысячи', 'тысяч', 1), // 10^ 3
            array('миллион', 'миллиона', 'миллионов', 0), // 10^ 6
            array('миллиард', 'миллиарда', 'миллиардов', 0), // 10^ 9
            array('триллион', 'триллиона', 'триллионов', 0), // 10^12
        );
        $out = $tmp = array();
        // Поехали!
        $tmp = explode('.', str_replace(',', '.', $inn));
        $rub = number_format($tmp[0], 0, '', '-');
        if ($rub == 0) $out[] = $nol;

        // нормализация копеек
        $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0, 2) : '00';
        $segments = explode('-', $rub);
        $offset = sizeof($segments);

        if ((int)$rub == 0) { // если 0 рублей
            $o[] = $nol;
            $o[] = self::morph(0, $forms[1][0], $forms[1][1], $forms[1][2]);
        } else {
            foreach ($segments as $k => $lev) {
                $sexi = (int)$forms[$offset][3]; // определяем род
                $ri = (int)$lev; // текущий сегмент
                if ($ri == 0 && $offset > 1) { // если сегмент==0 & не последний уровень(там Units)
                    $offset--;
                    continue;
                }

                // нормализация
                $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);

                // получаем циферки для анализа
                $r1 = (int)substr($ri, 0, 1); //первая цифра
                $r2 = (int)substr($ri, 1, 1); //вторая
                $r3 = (int)substr($ri, 2, 1); //третья
                $r22 = (int)$r2 . $r3; //вторая и третья

                // разгребаем порядки
                if ($ri > 99) $o[] = $str[100][$r1]; // Сотни
                if ($r22 > 20) { // >20
                    $o[] = $str[10][$r2];
                    $o[] = $sex[$sexi][$r3];
                } else { // <=20
                    if ($r22 > 9) $o[] = $str[11][$r22 - 9]; // 10-20
                    elseif ($r22 > 0) $o[] = $sex[$sexi][$r3]; // 1-9
                }

                // Рубли
                $o[] = self::morph($ri, $forms[$offset][0], $forms[$offset][1], $forms[$offset][2]);
                $offset--;
            }

        }

        // Копейки
        if (!$stripkop) {
            $o[] = $kop;
            $o[] = self::morph($kop, $forms[0][0], $forms[0][1], $forms[0][2]);
        }
        return preg_replace("/\s{2,}/", ' ', implode(' ', $o));
    }
}
