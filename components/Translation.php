<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 17.09.2015
 * Time: 21:44
 */

namespace ra\admin\components;


use ra\admin\models\Message;
use Yii;
use yii\helpers\FileHelper;
use yii\i18n\MissingTranslationEvent;

class Translation
{
    public static function handleMissingAdminTranslation(MissingTranslationEvent $event)
    {
        self::log($event->category, $event->message, $event->language);
        $event->translatedMessage = YII_ENV_DEV ? "@MISSING: {$event->category}.{$event->message} FOR LANGUAGE {$event->language} @" : $event->message;
    }

    static function log($category, $message, $language)
    {
        $filename = Yii::getAlias('@runtime/forTranslate/' . $language . '/' . $category . '.php');
        FileHelper::createDirectory(dirname($filename));
        if (file_exists($filename) && strpos(file_get_contents($filename), "'{$message}'")) return;
        file_put_contents($filename, "    '{$message}' => '',\n", FILE_APPEND);
    }

    public static function handleMissingTranslation(MissingTranslationEvent $event)
    {
        $event->translatedMessage = Message::add($event->category, $event->message, $event->language);
    }
}