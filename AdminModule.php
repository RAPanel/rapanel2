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

    public $controllerNamespace = 'app\admin\controllers';

    public function init()
    {
        // ставим алис для админки на текущую папку
        Yii::setAlias('@admin', __DIR__);

        $this->module->layout = '@admin/views/layouts/main.php';

        // импорт всех файлов конфигураций
        foreach (FileHelper::findFiles(Yii::getAlias('@admin/config'), ['except' => ['.php']]) as $file) {
            $data = require($file);
            foreach ($data as $key => $value)
                $data[$key] = ArrayHelper::merge($this->module->{$key}, $value);
            Yii::configure($this->module, $data);
        }

        parent::init();
    }
}