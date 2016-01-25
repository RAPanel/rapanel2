<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 16:22
 */

$email = 'no-reply@' . str_replace('www.', $_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST']);

$config = [
    'aliases' => [
        '@theme' => '@web/theme',
    ],
    'modules' => [
        'rapanel' => [
            'class' => 'ra\admin\AdminModule',
        ],
    ],
    'controllerMap' => [
        'image' => 'ra\admin\controllers\ImageController',
    ],
    'components' => [
        'cart' => [
            'class' => 'ra\admin\components\ShoppingCart',
        ],
        'translation' => [
            'class' => 'ra\admin\services\YandexTranslate',
            'key' => 'trnsl.1.1.20150430T103740Z.3bbb7c3d5fb6affa.0b2f8a6b338cce2e1b8b554495628fbd158d1784',
        ],
        'urlManager' => [
            'rules' => [
                '/' => 'site/index',
            ],
        ],
        'authManager' => [
            'class' => 'ra\admin\components\AuthManager',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'messageConfig' => [
                'from' => [$email],
                'charset' => 'UTF-8',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
        ],
    ],
];

return $config;