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
    'sourceLanguage' => 'en-US',
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<c_:image>/_<type>/<name>' => '<c_>/index',
                '<m_:rapanel>' => '<m_>/default/index',
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
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
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