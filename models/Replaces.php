<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%replaces}}".
 *
 * @property string $name
 * @property string $value
 * @property string $updated_at
 * @property string $created_at
 */
class Replaces extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%replaces}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['value'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('ra', 'Name'),
            'value' => Yii::t('ra', 'Value'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
        ];
    }
}
