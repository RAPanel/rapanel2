<?php

namespace app\admin\traits;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

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
        /** @var string $relationName */
        $relationName = 'get' . ucfirst($name);
        /** @var Query $relationMethod */
        $relationMethod = $this->$relationName();

        $insert = function ($event) use ($relationMethod) {
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($event->data as $row) {
                $modelClass = $relationMethod->modelClass;
                $attribute = reset($relationMethod->link);
                $row[key($relationMethod->link)] = $event->sender->{$attribute};
                Yii::$app->db->createCommand()->insert($modelClass::tableName(), $row)->execute();
            }
            $transaction->commit();
        };

        $delete = function ($event) use ($relationMethod) {
            $transaction = Yii::$app->db->beginTransaction();
            /** @var ActiveRecord $row */
            foreach ($relationMethod->all() as $row)
                $row->delete();
            $transaction->commit();
        };

        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, $delete, $value);
        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, $insert, $value);
        $this->on(ActiveRecord::EVENT_AFTER_INSERT, $insert, $value);
    }
}