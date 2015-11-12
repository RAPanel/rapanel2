<?php
namespace ra\admin;

use Yii;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 16:24
 */
class AdminModule extends \yii\base\Module
{
    public $settings;

    public $controllerNamespace = 'ra\admin\controllers';

    public function init()
    {
        $this->module->layout = '@ra/admin/views/layouts/main.php';

        // импорт всех файлов конфигураций
        Yii::$app->ra->configure($this->module, 'admin');

        parent::init();
    }
}