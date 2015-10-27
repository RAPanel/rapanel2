<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 29.05.2015
 * Time: 11:40
 */

namespace ra\admin\components;

use ra\admin\helpers\RA;
use ra\admin\models\arrays\Modules;
use ra\admin\models\Settings;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class RAComponent extends Component implements BootstrapInterface
{
    private $_modules;

    public function bootstrap($app)
    {
        $list = Settings::find()->select(['path', 'value'])->asArray()->all();
        $result = [];
        foreach ($list as $row) {
            $parse = explode('.', $row['path']);
            $data = $row['value'];
            foreach (array_reverse($parse) as $val) if ($val = trim($val))
                $data = array($val => $data);
            $result = ArrayHelper::merge($result, $data);
        }
        Yii::$app->params = ArrayHelper::merge(Yii::$app->params, $result);

        return true;
    }

    public function getSettings()
    {
        return RA::config();
    }

    public function getModules()
    {
        if (!$this->_modules)
            $this->_modules = new Modules;
        return $this->_modules;
    }
}