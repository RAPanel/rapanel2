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
        // Include clever debug module
        $this->debug();

        // Configure app
        $this->configure('default', false);
        foreach ($this->configList as $config)
            $this->configure($config);

        // Import params
        Yii::$app->params = $this->getParams();

        return true;
    }

    public function debug($debug = true)
    {
        if (in_array(php_uname('n'), ['localhost', 'devhost'])) {
            $debug = true;
            defined('YII_ENV') or define('YII_ENV', 'dev');
            defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);
        }

        if ($debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
            ini_set('html_errors', true);

            defined('YII_DEBUG') or define('YII_DEBUG', true);
        }
    }

    public function configure($name, $priority = true)
    {
        $file = Yii::getAlias('@ra/admin/config/' . $name . '.php');
        if (!file_exists($file))
            throw new HttpException(400, Yii::t('ra', 'Can`t find file "{file}"', ['file' => $file]));
        $data = require(Yii::getAlias('@ra/admin/config/' . $name . '.php'));
        foreach ($data as $key => $value) if (isset(Yii::$app->{$key})) {
            if (!$priority && Yii::$app->{$key} || Yii::$app->{$key} == $value)
                continue;
            if (is_array(Yii::$app->{$key}) && is_array($value))
                Yii::$app->$key = ArrayHelper::merge(Yii::$app->{$key}, $value);
            else
                Yii::$app->$key = $value;
        }
    }

    public function getParams()
    {
        if ($this->_params === false) {
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