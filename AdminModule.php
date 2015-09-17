<?php
namespace app\admin;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 16:24
 */
class AdminModule extends \yii\base\Module
{
    public $settings;

    public $layout = '@admin/views/layout/main.php';

    public $controllerNamespace = 'app\admin\controllers';

    public function init()
    {
        // ставим алис для админки на текущую папку
        Yii::setAlias('@admin', __DIR__);

        // импорт всех файлов конфигураций
        foreach (FileHelper::findFiles(Yii::getAlias('@admin/config'), ['except' => ['.php']]) as $file)
            Yii::configure($this, require($file));

        parent::init();

        $this->registerTranslations();

        // инициализация модуля с помощью конфигурации, загруженной из config.php
        Yii::$app->setModule('user', [
            'class' => 'rere\user\Module',
            'modelClasses' => [
                'Role' => 'rere\core\models\Role'
            ],
        ]);
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['ra/*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath'       => '@app/admin/messages',
            'on missingTranslation' => ['app\admin\components\Translation', 'handleMissingAdminTranslation']
        ];
    }
}