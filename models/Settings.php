<?php

namespace app\admin\models;

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
            'id' => Yii::t('ra/model', 'ID'),
            'path' => Yii::t('ra/model', 'Path'),
            'inputType' => Yii::t('ra/model', 'Input Type'),
            'name' => Yii::t('ra/model', 'Name'),
            'value' => Yii::t('ra/model', 'Value'),
            'updated_at' => Yii::t('ra/model', 'Updated At'),
            'created_at' => Yii::t('ra/model', 'Created At'),
        ];
    }
}
