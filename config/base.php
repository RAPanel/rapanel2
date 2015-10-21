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
        'i18n' => [
            'class' => 'yii\i18n\I18N',
            'translations' => [
                'ra/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@app/admin/messages',
                    'on missingTranslation' => ['app\admin\components\Translation', 'handleMissingAdminTranslation']
                ],
            ]
        ],
        'user' => [
            'identityClass' => 'app\admin\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'rapanel/default/error',
        ],
    ],
];