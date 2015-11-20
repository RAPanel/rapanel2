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
use yii\web\HttpException;

class RAComponent extends Component implements BootstrapInterface
{
    public $configList = ['core'];

    private $_modules;
    private $_params;

    public function bootstrap($app)
    {
        // Configure app
        $this->configure($app, 'default', false);
        foreach ($this->configList as $config)
            $this->configure($app, $config);

        // Import params
        $app->params = $this->getParams();

        return true;
    }

    public function configure($app, $name, $priority = true)
    {
        $file = Yii::getAlias('@ra/admin/config/' . $name . '.php');
        if (!file_exists($file))
            throw new HttpException(400, Yii::t('ra', 'Can`t find file "{file}"', ['file' => $file]));
        $data = require(Yii::getAlias('@ra/admin/config/' . $name . '.php'));
        foreach ($data as $key => $value)
            if ($key == 'aliases') {
                $app->setAliases($value);
            } elseif (isset(Yii::$app->{$key})) {
                if ($app->{$key} == $value)
                    continue;
                elseif (is_array(Yii::$app->{$key}) && is_array($value))
                    $app->$key = $priority ? ArrayHelper::merge(Yii::$app->{$key}, $value) : ArrayHelper::merge($value, Yii::$app->{$key});
                else
                    $app->$key = $value;
            }
    }

    public function getParams()
    {
        if (is_null($this->_params)) {
            $this->_params = Yii::$app->params;
            $list = Settings::find()->select(['path', 'value'])->asArray()->all();
            foreach ($list as $row) {
                $parse = explode('.', $row['path']);
                $data = $row['value'];
                foreach (array_reverse($parse) as $val)
                    if ($val = trim($val))
                        $data = array($val => $data);
                $this->_params = ArrayHelper::merge($this->_params, $data);
            }
        }

        return $this->_params;
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