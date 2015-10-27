<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%settings}}".
 *
 * @property string $id
 * @property string $path
 * @property string $inputType
 * @property string $name
 * @property string $value
 * @property string $updated_at
 * @property string $created_at
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path', 'name'], 'required'],
            [['value'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['path', 'name'], 'string', 'max' => 64],
            [['inputType'], 'string', 'max' => 8]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'path' => Yii::t('ra', 'Path'),
            'inputType' => Yii::t('ra', 'Input Type'),
            'name' => Yii::t('ra', 'Name'),
            'value' => Yii::t('ra', 'Value'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
        ];
    }
}
