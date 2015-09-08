<?php

namespace app\admin\models;

use Yii;

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
            'module_id' => Yii::t('rere.model', 'Module ID'),
            'sort' => Yii::t('rere.model', 'Sort'),
            'url' => Yii::t('rere.model', 'Url'),
            'value' => Yii::t('rere.model', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }
}
