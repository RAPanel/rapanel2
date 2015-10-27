<?php

namespace ra\admin\traits;

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
        if (!$this instanceof ActiveRecord) return;

//        $relationName = 'get' . ucfirst($name);
        /** @var Query $relationMethod */
        $relationMethod = $this->{'get' . ucfirst($name)}();

        $relationData = $this->isNewRecord ? [] : $this->{$name};

        $function = function ($event) use ($relationMethod, $relationData) {
            $transaction = Yii::$app->db->beginTransaction();

            $pkAttribute = key($relationMethod->link);
            $extendAttribute = reset($relationMethod->link);
            $keyValue = $event->sender->{$extendAttribute};

            $relationMap = [];
            foreach ($relationData as $row)
                $relationMap[$row[$pkAttribute]] = $row;

            foreach ($event->data as $key => $row) {
                /** @var ActiveRecord $model */
                $model = isset($relationMap[$keyValue]) ? $relationMap[$keyValue] : new $relationMethod->modelClass;
                if (!$model->isNewRecord) unset($relationMap[$keyValue]);
                $model->setAttributes($row);
                $model->save();
            }

            $transaction->commit();
        };

        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, $function, $value);
        $this->on(ActiveRecord::EVENT_AFTER_INSERT, $function, $value);
    }
}