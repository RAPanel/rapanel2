<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%replaces}}".
 *
 * @property string $name
 * @property string $value
 * @property string $update_at
 * @property string $create_at
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
            [['update_at', 'create_at'], 'safe'],
            [['name'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('ra/model', 'Name'),
            'value' => Yii::t('ra/model', 'Value'),
            'update_at' => Yii::t('ra/model', 'Update At'),
            'create_at' => Yii::t('ra/model', 'Create At'),
        ];
    }
}
