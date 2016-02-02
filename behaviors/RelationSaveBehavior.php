<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 24.09.2015
 * Time: 22:11
 */

namespace ra\admin\behaviors;


use Yii;
use yii\base\Behavior;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

class RelationSaveBehavior extends Behavior
{
    /**
     * Stores a list of relations, affected by the behavior. Configurable property.
     * @var array
     */
    public $relations = [];
    /**
     * Stores values of relation attributes. All entries in this array are considered
     * dirty (changed) attributes and will be saved in saveRelations().
     * @var array
     */
    private $_values = [];

    /**
     * Events list
     * @return array
     */
    public function events()
    {
        $events = parent::events();
        $events[ActiveRecord::EVENT_AFTER_INSERT] = 'saveRelations';
        $events[ActiveRecord::EVENT_AFTER_UPDATE] = 'saveRelations';

        return $events;
    }

    /**
     * Save all dirty (changed) relation values ($this->_values) to the database
     * @param $event
     * @throws ErrorException
     * @throws \yii\db\Exception
     */
    public function saveRelations($event)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        foreach ($this->relations as $relationName => $params) {
            if (is_numeric($relationName)) $relationName = $params;
            if (!isset($this->_values[$relationName])) continue;

            $relationGetter = 'get' . ucfirst($relationName);

            if (!method_exists($owner, $relationGetter))
                throw new ErrorException("Can`t find relation \"{$relationGetter}\" in " . $owner::className());

            /** @var ActiveQuery $relationSettings */
            $relationSettings = $owner->$relationGetter();
            /** @var ActiveRecord $relationClass */
            $relationClass = $relationSettings->modelClass;

            $data = $this->_values[$relationName] ? $this->_values[$relationName] : [];
            $key = key($relationSettings->link);
            $attribute = current($relationSettings->link);

            // one-to-many on the one side
            if (!empty($relationSettings->link) && $relationSettings->multiple) {
                $keys = $values = [];
                foreach ($data as $i => $row) {
                    $row[$key] = $owner->{$attribute};
                    $keys = array_merge($keys, array_keys($row));
                    foreach ($keys as $number) $values[$i][] = $row[$number];
                }

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    /** @var ActiveRecord $row */
                    foreach ($owner->$relationName as $row) $row->delete();
                    if (count($values))
                        $relationClass::getDb()->createCommand()->batchInsert($relationClass::tableName(), $keys, $values)->execute();

                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                    throw $ex;
                }
            }
        }
    }

    /**
     * Sets the value of a component property. The data is passed
     *
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @throws ErrorException
     */
    public function __set($name, $value)
    {
        $relationParams = isset($this->relations[$name]) ? $this->relations[$name] : null;
        if (!empty($relationParams['set'])) {
            $this->_values[$name] = $this->callUserFunction($relationParams['set'], $value);
        } else {
            $this->_values[$name] = $value;
        }
    }

    /**
     * Call user function
     * @param $function
     * @param $value
     * @return mixed
     * @throws ErrorException
     */
    private function callUserFunction($function, $value)
    {
        if (!is_array($function) && !$function instanceof \Closure) {
            throw new ErrorException("This value is not a function");
        }
        return call_user_func($function, $value);
    }

    /**
     * Returns a value indicating whether a property can be read.
     * We return true if it is one of our properties and pass the
     * params on to the parent class otherwise.
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     * @return boolean whether the property can be read
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return array_key_exists($name, $this->relations) || in_array($name, $this->relations) ?
            true : parent::canSetProperty($name, $checkVars);
    }

}