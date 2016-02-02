<?php

namespace ra\admin\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%module_settings}}".
 *
 * @property string $module_id
 * @property string $sort
 * @property string $url
 * @property string $value
 *
 * @property Module $module
 */
class ModuleSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%module_settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'url', 'value'], 'required'],
            [['module_id', 'sort'], 'integer'],
            [['url'], 'string', 'max' => 16],
            [['value'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module_id' => Yii::t('ra', 'Module ID'),
            'sort' => Yii::t('ra', 'Sort'),
            'url' => Yii::t('ra', 'Url'),
            'value' => Yii::t('ra', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    public function init()
    {
        parent::init();
        $serialize = function ($event) {
            if (is_array($event->sender->value) || is_object($event->sender->value)) {
                $event->sender->value = serialize($event->sender->value);
            }
        };
        $this->on($this::EVENT_AFTER_VALIDATE, $serialize);
        $this->on($this::EVENT_BEFORE_INSERT, $serialize);
        $this->on($this::EVENT_BEFORE_UPDATE, $serialize);
        $this->on($this::EVENT_AFTER_FIND, function ($event) {
            if ($event->sender->value) {
                try {
                    $value = @unserialize($event->sender->value);
                    if ($value) $event->sender->value = $value;
                } catch (Exception $e) {
                }
            }
        });
    }
}
