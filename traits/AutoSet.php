<?php

namespace app\admin\traits;
use Yii;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 11.09.2015
 * Time: 23:43
 */
trait AutoSet
{
    public function setRelations($name, $value)
    {
        $relationName = 'get' . ucfirst($name);
        $relationMethod = $this->$relationName();

        $this->on(self::EVENT_AFTER_INSERT, function ($event) use ($relationMethod) {
            foreach ($event->data as $row) {
                $modelClass = $relationMethod->modelClass;
                $attribute = reset($relationMethod->link);
                $row[key($relationMethod->link)] = $event->sender->{$attribute};
                Yii::$app->db->createCommand()->insert($modelClass::tableName(), $row)->execute();
            }
        }, $value);
    }
}