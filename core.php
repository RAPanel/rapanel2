<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 16:22
 */

$email = 'no-reply@' . str_replace('www.', $_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST']);

$config = [
    'id' => 'rere',
    'bootstrap' => ['raInit'],
    'language' => 'en-US',
    'aliases' => [
        '@theme' => '/theme',
    ],
    'modules' => [
        'rapanel' => [
            'class' => 'app\admin\AdminModule',
        ],
    ],
    'controllerMap' => [
        'image' => 'app\admin\controllers\ImageController',
    ],
    'components' => [
        'translation' => [
            'class' => 'wfstudioru\translate\Translation',
            'key' => 'trnsl.1.1.20150430T103740Z.3bbb7c3d5fb6affa.0b2f8a6b338cce2e1b8b554495628fbd158d1784',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
                '<c_:image>/_<type>/<name>' => '<c_>/index',
                '<m_:rapanel>' => '<m_>/default/index',
            ],
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
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'lVZD-kar4vfyeUGHVnVZbijmWiwgteuU',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'messageTable' => '{{%message_translate}}',
                    'sourceMessageTable' => '{{%message}}',
                    'on missingTranslation' => ['app\admin\components\Translation', 'handleMissingTranslation'],
                ],
            ],
        ],
        /*'i18n' => [
            'class' => Zelenin\yii\modules\I18n\components\I18N::className(),
            'languages' => ['ru-RU'],
            'messageTable' => '{{%message_translate}}',
            'sourceMessageTable' => '{{%message}}',
        ],*/
        'raInit' => [
            'class' => 'app\admin\components\RAInit',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => [
        'adminEmail' => 'webmaster@rere-design.ru',
        'fromEmail' => $email,
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