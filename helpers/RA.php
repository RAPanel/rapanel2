<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 03.09.2015
 * Time: 17:33
 */

namespace app\admin\helpers;


use app\admin\models\Character;
use app\admin\models\Module;
use app\admin\models\ModuleSettings;
use app\admin\models\User;
use ArrayObject;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\HttpException;

class RA
{
    static $tabs = ['main', 'data', 'seo', 'position', 'characters', 'photos'];
    static $_cache = [];

    public static function tabs()
    {
        return self::dropDownList(self::$tabs, 'ra/tabs');
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param array $values
     * @return array
     */
    public static function tableColumns($model, $values = [])
    {
        if (!class_exists($model)) return [];
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

        return self::dropDownList($columns, 'ra/model');
    }

    /**
     * @param $data array
     * @param string $alias
     * @return array
     */
    public static function dropDownList(array $data, $alias = 'ra/dropDown')
    {
        $list = [];
        foreach ($data as $key => $value)
            $list[is_int($key) ? $value : $key] = $alias ? Yii::t($alias, Inflector::camel2words($value, true)) : $value;
        return $list;
    }

    public static function cache($method, $function)
    {
        if (!isset(self::$_cache[$method])) {
            self::$_cache[$method] = call_user_func($function);
        }
        return self::$_cache[$method];
    }

    public static function moduleId($module)
    {
        if (!is_numeric($module)) return self::module($module);
        return $module;
    }

    public static function module($value = null, $return = 'url')
    {
        $data = self::cache(serialize([__METHOD__, $return]), function () use ($return) {
            return ArrayHelper::map(Module::find()->select(['id', $return])->asArray()->all(), 'id', $return);
        });
        if (is_null($value)) return $data;
        if (is_numeric($value) && isset($data[$value])) return $data[$value];
        elseif (is_string($value) && ($data = array_flip($data)) && isset($data[$value])) return $data[$value];
        return false;
    }

    public static function moduleSetting($module, $name = null)
    {
        $module_id = self::moduleId($module);
        $data = self::cache(serialize([__METHOD__, $module_id]), function () use ($module_id) {
            return ArrayHelper::map(ModuleSettings::find()->where(compact('module_id'))->select(['url', 'value'])->asArray()->all(), 'url', 'value');
        });
        if (is_null($name)) return $data;
        return isset($data[$name]) ? $data[$name] : false;
    }

    public static function character($value = null, $return = 'url')
    {
        $data = self::cache(serialize([__METHOD__, $return]), function () use ($return) {
            return ArrayHelper::map(Character::find()->select(['id', $return])->asArray()->all(), 'id', $return);
        });
        if (is_null($value)) return $data;
        if (is_numeric($value) && isset($data[$value])) return $data[$value];
        elseif (is_string($value) && ($data = array_flip($data)) && isset($data[$value])) return $data[$value];
        elseif (is_array($value)) {
            if (is_string(reset($value))) $data = array_flip($data);
            $result = [];
            foreach ($value as $key => $row)
                if (isset($data[$row]))
                    $result[$key] = $data[$row];
            return $result;
        };
        return false;
    }

    public static function characterCondition($name, $value = false)
    {
        return ['pageCharacters' => function ($query) use ($name, $value) {
            /** @var $query \yii\db\ActiveQuery */
            /** @var \yii\db\ActiveRecord $class */
            $class = $query->modelClass;
            if (is_array($name)) {
                $tableName = key($name);
                $name = reset($name);
            } else
                $tableName = $name;
            $query->from([$tableName => $class::tableName()])->where(ArrayHelper::merge([
                $tableName . '.character_id' => RA::character($name),
            ], $value ? [$tableName . '.value' => $value] : []));
        }];
    }

    /**
     * @param $query \yii\db\ActiveQuery
     * @return array
     */
    public static function pageItems($query)
    {
        $query->orderBy(ArrayHelper::merge(['is_category' => SORT_ASC, 'level' => SORT_DESC], $query->orderBy));
        $query->addOrderBy(['lft' => SORT_ASC, 'id' => SORT_ASC]);
        $items = [];
        /** @var \app\admin\models\Page $row */
        foreach ($query->all() as $row) {
            $items[$row->parent_id][] = [
                'label' => $row->name,
                'url' => $row->getHref(0),
                'items' => isset($items[$row->id]) ? $items[$row->id] : null,
            ];
        }

        return isset($row) ? end($items) : [];
    }

    public static function info($name = false, $default = null)
    {
        return isset(Yii::$app->params[$name]) ? Yii::$app->params[$name] : $default;
    }

    public static function config($type = null)
    {
        $data = [
            'user' => [
                'models' => [
                    'user' => User::className(),
                ],
                'loginEmail' => true,
                'loginUsername' => false,
                'loginDuration' => 60 * 60 * 30,
                'loginRedirect' => '/',
                'logoutRedirect' => null,
            ],
        ];
        return is_null($type) ? $data : $data[$type];
    }
}