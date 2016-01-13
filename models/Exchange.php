<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%exchange}}".
 *
 * @property string $id
 * @property string $type
 * @property string $value
 */
class Exchange extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%exchange}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'value'], 'required'],
            [['value'], 'integer'],
            [['id'], 'string', 'max' => 36],
            [['type'], 'string', 'max' => 8],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'type' => Yii::t('ra', 'Type'),
            'value' => Yii::t('ra', 'Value'),
        ];
    }
}
