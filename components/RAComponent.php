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
use ra\admin\models\Character;
use ra\admin\models\Settings;
use ra\admin\widgets\siteWidget\SiteAsset;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\Application;
use yii\web\HttpException;

class RAComponent extends Component implements BootstrapInterface
{
    public $configList = ['core'];

    private $_modules;
    private $_params;
    private $_cache;

    public function bootstrap($app)
    {
        if(Yii::$app->user->can('admin'))
            defined('YII_DEBUG') or define('YII_DEBUG', !empty($_COOKIE['debug']));

        if (YII_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
            ini_set('html_errors', true);
        }

        // Configure app
        $this->configure($app, 'default', false);
        foreach ($this->configList as $config)
            $this->configure($app, $config);

        if (Yii::$app->request->cookies->getValue('canAdmin'))
            $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
                SiteAsset::register(Yii::$app->view);
            });

        // Import params
        $app->params = $this->getParams();

        return true;

    }

    public function configure($app, $name, $priority = true)
    {
        if (is_string($name)) {
            $file = Yii::getAlias('@ra/admin/config/' . $name . '.php');
            if (!file_exists($file))
                throw new HttpException(400, Yii::t('ra', 'Can`t find file "{file}"', ['file' => $file]));
            $data = require(Yii::getAlias('@ra/admin/config/' . $name . '.php'));
        } else $data = $name;
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

    public function getCharacterId($url)
    {
        if (is_numeric($url)) return $url;
        return isset($this->getCharacters(true)[$url]) ? $this->getCharacters(true)[$url] : null;
    }

    public function getCharacters($byUrl = false)
    {
        $characters = $this->cache(__METHOD__, function () {
            $query = Character::find()->select(['id', 'url'])->asArray();
            return ArrayHelper::map($query->all(), 'id', 'url');
        });
        if ($byUrl) return array_flip($characters);
        return $characters;
    }

    protected function cache($method, $function, $data = false)
    {
        if (!isset($this->_cache[$method])) {
            $this->_cache[$method] = call_user_func($function, $data);
        }
        return $this->_cache[$method];
    }

    public function getCharacterUrl($id)
    {
        if (!is_numeric($id)) return $id;
        return isset($this->getCharacters(false)[$id]) ? $this->getCharacters(true)[$id] : null;
    }

    public function getCharacterName($url)
    {
        return Yii::t('app\character', Inflector::camel2words($url));
    }
}