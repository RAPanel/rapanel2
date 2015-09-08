<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 03.09.2015
 * Time: 17:33
 */

namespace app\admin\helpers;


use app\admin\models\Module;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class RA
{
    static $tabs = ['main', 'data', 'seo', 'position', 'characters', 'photos'];
    static $_cache = [];

    public static function tabs()
    {
        return self::dropDownList(self::$tabs, 'rere.tabs');
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param array $values
     * @return array
     */
    public static function tableColumns($model, $values = [])
    {
        try {
            $model = new $model;
            $columns = [];
            foreach ($model->getTableSchema()->columns as $key => $value) {
                if ($key == 'module_id') continue;
                if (strpos($key, '_id')) {
                    $name = str_replace('_id', '', $key);
                    $get = 'get' . ucfirst($name);
                    if (method_exists($model, $get)) {
                        $modelClass = $model->$get()->modelClass;
                        /** @var \yii\db\ActiveRecord $class */
                        $class = new $modelClass;
                        foreach (array_keys($class->attributes) as $attribute)
                            if (in_array($attribute, ['username', 'name', 'value']))
                                break;
                        if (isset($attribute)) {
                            $key = "{$name}.{$attribute}";
                        }
                    }
                }

                $columns[$key] = $key;
            }

            if ($values) {
                $columnsResort = $columns;
                $columns = [];
                foreach ($values as $key)
                    if (isset($columnsResort[$key]))
                        $columns[$key] = $columnsResort[$key];
                $columns = ArrayHelper::merge($columns, $columnsResort);
            }

            return self::dropDownList($columns, 'rere.model');
        } catch (Exception $e) {
            return [];
        }

    }

    /**
     * @param $data array
     * @param string $alias
     * @return array
     */
    public static function dropDownList(array $data, $alias = 'rere.dropdown')
    {
        $list = [];
        foreach ($data as $key => $value)
            $list[is_int($key) ? $value : $key] = Yii::t($alias, mb_convert_case(trim(preg_replace('#[^\w]#', ' ', $value)), MB_CASE_TITLE));
        return $list;
    }

    public static function cache($method, $function)
    {
        if (!isset(self::$_cache[$method])) {
            self::$_cache[$method] = call_user_func($function);
        }
        return self::$_cache[$method];
    }

    public static function module($value = null, $return = 'url')
    {
        $data = self::cache(serialize([__METHOD__, $return]), function () use ($return) {
            return ArrayHelper::map(Module::find()->select(['id', $return])->asArray()->all(), 'id', $return);
        });
        if (is_numeric($value)) return $data[$value];
        elseif (is_string($value)) return array_reverse($data)[$value];
        else return $data;
    }
}