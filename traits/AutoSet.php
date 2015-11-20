<?php

namespace ra\admin\traits;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 11.09.2015
 * Time: 23:43
 */
trait AutoSet
{
    public function setRelation($name, $value, $settings = [])
    {
        if (!$this instanceof ActiveRecord) return;

        $settings = ArrayHelper::merge([
            'pk' => null,
            'validation' => true,
            'safeOnly' => true,
        ], $settings);

        $function = function ($event) use ($name, $settings) {
            $transaction = Yii::$app->db->beginTransaction();

            /** @var Query $relationMethod */
            $relationMethod = $this->{'get' . ucfirst($name)}();

            $modelClass = $relationMethod->modelClass;
            $pkAttribute = key($relationMethod->link);
            $extendAttribute = reset($relationMethod->link);

            $relationData = $event->sender->isNewRecord ? [] : $event->sender->{$name};

            if ($relationMethod->multiple) {
                $modelPK = is_null($settings['pk']) ? $modelClass::primaryKey() : (array)$settings['pk'];

                $relationMap = [];
                foreach ($relationData as $row) {
                    $key = '';
                    foreach ($modelPK as $value)
                        if (isset($row->{$value}))
                            $key .= "{$value}:{$row->{$value}};";

                    $relationMap[$key] = $row;
                }

                if (is_array($event->data)) foreach ($event->data as $row) {
                    $key = '';
                    foreach ($modelPK as $value)
                        if (isset($row[$value]))
                            $key .= "{$value}:{$row[$value]};";

                    /** @var ActiveRecord $model */
                    $model = isset($relationMap[$key]) ? $relationMap[$key] : new $modelClass;
                    if (!$model->isNewRecord) unset($relationMap[$key]);
                    else $model->{$pkAttribute} = $this->{$extendAttribute};
                    $model->setAttributes($row, $settings['safeOnly']);
                    $model->save($settings['validation']);
                }

                /** @var ActiveRecord $row */
                foreach ($relationMap as $row)
                    $row->delete();
            } else {
                /** @var ActiveRecord $model */
                $model = $relationData ?: new $modelClass;
                if ($model->isNewRecord)
                    $model->{$pkAttribute} = $this->{$extendAttribute};
                $model->setAttributes($event->data, $settings['safeOnly']);
                $model->save($settings['validation']);
            }

            $transaction->commit();
        };

        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, $function, $value);
        $this->on(ActiveRecord::EVENT_AFTER_INSERT, $function, $value);
    }
}