<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 03.09.2015
 * Time: 17:33
 */

namespace ra\admin\helpers;


use ra\admin\models\Character;
use ra\admin\models\Module;
use ra\admin\models\ModuleSettings;
use ra\admin\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class RA
{
    static $_cache = [];

    /**
     * @param \yii\db\ActiveRecord $model
     * @param array $values
     * @return array
     */
    public static function tableColumns($model, $values = [])
    {
        if (!class_exists($model)) return [];
        if (!is_object($model)) $model = new $model;
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

        return self::dropDownList($columns, 'ra');
    }

    /**
     * @param $data array
     * @param string $alias
     * @return array
     */
    public static function dropDownList(array $data, $alias = 'app')
    {
        $list = [];
        foreach ($data as $key => $value)
            $list[is_int($key) ? $value : $key] = $alias ? Yii::t($alias, Inflector::camel2words($value, true)) : $value;
        return $list;
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

    public static function cache($method, $function)
    {
        if (!isset(self::$_cache[$method])) {
            self::$_cache[$method] = call_user_func($function);
        }
        return self::$_cache[$method];
    }

    public static function characterCondition($name, $value = null, $type = null)
    {
        return ['pageCharacters' => function ($query) use ($name, $value, $type) {
            /** @var $query \yii\db\ActiveQuery */
            /** @var \yii\db\ActiveRecord $class */
            $class = $query->modelClass;
            if (is_array($name)) {
                $tableName = key($name);
                $name = reset($name);
            } else
                $tableName = $name;
            $query->from([$tableName => $class::tableName()])->andWhere([$tableName . '.character_id' => RA::character($name)]);
            if (!is_null($value)) $query->andWhere($type ? [$type, $tableName . '.value', $value] : [$tableName . '.value' => $value]);
        }];
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

    public static function moduleCharacters($module, $url = false)
    {
        $module_id = self::moduleId($module);
        $data = self::cache(serialize([__METHOD__]), function () use ($module_id) {
            $query = Character::find()->joinWith('characterShows')->where(['module_id' => $module_id])->select(['id', 'character_id', 'url'])->asArray();
            return ArrayHelper::map($query->all(), 'character_id', 'url');
        });
        return $url ? array_search($url, $data) : $data;
    }

    /**
     * @param $query \yii\db\ActiveQuery
     * @return array
     */
    public static function pageItems($query, $order = ['is_category' => SORT_ASC, 'level' => SORT_DESC, 'lft' => SORT_ASC, 'id' => SORT_ASC], $levels = true)
    {
        if (is_array($query->orderBy) && is_array($order)) $query->orderBy(array_merge_recursive($order, $query->orderBy));
        if (empty($query->orderBy)) $query->orderBy($order);
        $query->andWhere(['status' => 1]);
        $items = [];
        /** @var \ra\admin\models\Page $row */
        foreach ($query->all() as $row) {
            $items[$levels ? $row->parent_id : 0][] = [
                'label' => $row->name,
                'url' => $row->getHref(0),
                'options' => ['class' => $row->url],
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
                'requireEmail' => true,
                'requireUsername' => true,
                'loginDuration' => 60 * 60 * 30 * 24,
                'loginRedirect' => '/',
                'logoutRedirect' => null,
            ],
        ];
        return is_null($type) ? $data : $data[$type];
    }

    public static function multiImplode($sep, $array) {
        $_array = [];
        foreach($array as $val)
            $_array[] = is_array($val)? self::multiImplode($sep, $val) : $val;
        return implode($sep, $_array);
    }
}