<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%index_data}}".
 *
 * @property string $id
 * @property string $value
 *
 * @property Index[] $indices
 */
class IndexData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%index_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            [['value'], 'string', 'max' => 64],
            [['value'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'value' => Yii::t('ra', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIndices()
    {
        return $this->hasMany(Index::className(), ['data_id' => 'id']);
    }
}
