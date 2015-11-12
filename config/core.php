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
        '@theme' => '/theme',
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
        'translation' => [
            'class' => 'wfstudioru\translate\Translation',
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
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@webroot/theme/views',
                    '@app/widgets/views' => '@webroot/theme/views/widgets',
                ],
                'baseUrl' => '@web/themes',
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'messageConfig' => [
                'from' => [$email],
                'charset' => 'UTF-8',
            ],
        ],
        'i18n' => [
            'translations' => [
                'ra' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@ra/admin/messages',
                ],
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'messageTable' => '{{%message_translate}}',
                    'sourceMessageTable' => '{{%message}}',
                    'on missingTranslation' => ['ra\admin\components\Translation', 'handleMissingTranslation'],
                ],
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
    ],
];

if (YII_ENV_DEV) {
    $allowedIPs = ['192.168.1.*', '78.159.225.99', '10.*.*.*'];
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['bootstrap'][] = 'gii';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => $allowedIPs,
    ];
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => $allowedIPs,
    ];
}

return $config;