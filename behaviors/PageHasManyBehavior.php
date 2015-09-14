<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 20:58
 *
 * @property \yii\db\ActiveRecord $owner
 */

namespace app\admin\behaviors;


use yii\base\Behavior;
use yii\base\Object;

class PageHasManyBehavior extends Behavior
{
    public function set($value, $relation)
    {
        /** @var \yii\db\ActiveRecord $owner */
        $owner = $this->owner;
        $relationName = 'get' . ucfirst($relation);
        /** @var \yii\db\ActiveRecord $moduleClass */
        $moduleClassName = $owner->{$relationName}()->modelClass;
        $link = $owner->{$relationName}()->link;

        $function = function ($event) use ($moduleClassName, $relation, $link) {
            /** @var \yii\db\ActiveRecord $moduleClass */
            $moduleClass = new $moduleClassName;
            $from = key($link);
            $to = reset($link);
            $moduleClass->{$from} = $event->sender->{$to};

            if ($event->sender->{$relation}) {
                /** @var \yii\db\ActiveRecord $row */
                foreach ($event->sender->{$relation} as $row) {
                    foreach ($event->data as $key => $value)
                        if (isset($value['id']) && ($exist = $value['id'] == $row->id))
                            break;

                    if (!empty($exist) && isset($key) && isset($value)) {
                        $row->setAttributes($value);
                        $row->save(false);
                        unset($event->data[$key]);
                    } else
                        $row->delete();
                }
            }

            if (!empty($event->data))
                foreach ($event->data as $value) {
                    $model = clone $moduleClass;
                    $model->setAttributes($value, false);
                    $model->save(false);
                }
        };

        if (!empty($value))
            $owner->on($owner::EVENT_AFTER_INSERT, $function, $value);
        $owner->on($owner::EVENT_AFTER_UPDATE, $function, $value);
    }
}