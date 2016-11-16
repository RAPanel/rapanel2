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
        'session' => [
            'class' => 'yii\web\DbSession',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'messageConfig' => [
                'from' => [$email],
                'charset' => 'UTF-8',
            ],
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['192.168.*.*', '78.159.225.99'],
    ];
}

if(YII_DEBUG){
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['192.168.*.*', '78.159.225.99'],
    ];

    $config['components']['assetManager']['forceCopy'] = true;
    $config['components']['log']['traceLevel'] = 3;
    $config['components']['cache']['class'] = '\yii\caching\DummyCache';
}

return $config;