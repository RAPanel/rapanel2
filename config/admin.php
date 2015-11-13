<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 16:22
 */

return [
    /*'modules' => [
        'user' => [
            'class' => 'rere\user\Module',
            'modelClasses' => [
                'Role' => 'rere\core\models\Role'
            ],
        ],
    ],*/
    'components' => [
        'urlManager' => [
//            'enablePrettyUrl' => true,
//            'showScriptName' => false,
            'rules' => [
                '<m_:rapanel>/<a_:auth>/<authclient>' => '<m_>/default/<a_>',
                '<m_:rapanel>' => '<m_>/default/index',
            ],
        ],
        'i18n' => [
            'class' => 'yii\i18n\I18N',
            'translations' => [
                'ra' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@ra/admin/messages',
                    'on missingTranslation' => ['ra\admin\components\Translation', 'handleMissingAdminTranslation']
                ],
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'messageTable' => '{{%message_translate}}',
                    'sourceMessageTable' => '{{%message}}',
                    'on missingTranslation' => ['ra\admin\components\Translation', 'handleMissingTranslation'],
                ],
            ]
        ],
        'user' => [
            'identityClass' => 'ra\admin\models\User',
            'loginUrl' => ['rapanel/default/login'],
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'rapanel/default/error',
        ],
    ],
];