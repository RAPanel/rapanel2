<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 16:22
 */

use yii\i18n\I18N;

return [
    'aliases' => [
    ],
    'components' => [
        'i18n' => [
            'class' => Zelenin\yii\modules\I18n\components\I18N::className(),
            'languages' => ['ru-RU'],
            'messageTable' => '{{%message_translate}}',
            'sourceMessageTable' => '{{%message}}',
        ],
    ],
];