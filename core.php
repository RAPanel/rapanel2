<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 16:22
 */

$config = [
    'bootstrap' => ['raInit'],
    'language' => 'en-US',
    'aliases' => [
        '@theme' => '/theme',
    ],
    'modules' => [
        'rapanel' => [
            'class' => 'app\admin\AdminModule',
            'settings' => [
                'rere\admin\actions\ModulesAction' => 'Модули и их конфигурация',
                'rere\admin\actions\ParamsAction' => 'Параметры и настройки',
                'rere\admin\actions\ReplacesAction' => 'Список замен в редакторе',
                'rere\admin\actions\UsersAction' => 'Список пользователей сайта',
                'rere\admin\actions\UpdateAction' => 'Обновление модулей',
            ],
        ],
    ],
    'components'=>[
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
                '<c_:image>/_<type>/<name>' => '<c_>/index',
                '<c_:rapanel>' => '<c_>/default/index',
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
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'lVZD-kar4vfyeUGHVnVZbijmWiwgteuU',
        ],
        'i18n' => [
            'class' => Zelenin\yii\modules\I18n\components\I18N::className(),
            'languages' => ['ru-RU'],
            'messageTable' => '{{%message_translate}}',
            'sourceMessageTable' => '{{%message}}',
        ],
        'raInit' => [
            'class' => 'app\admin\components\RAInit',
        ],
    ]
];

if (YII_ENV_DEV) {
    $allowedIPs = ['192.168.91.1', '78.159.225.99', '192.168.228.1', '192.168.1.1', '10.0.2.2'];
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