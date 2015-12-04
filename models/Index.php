<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%index}}".
 *
 * @property string $owner_id
 * @property string $model
 * @property string $type
 * @property string $data_id
 *
 * @property IndexData $data
 */
class Index extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%index}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['owner_id', 'model', 'type', 'data_id'], 'required'],
            [['owner_id', 'data_id'], 'integer'],
            [['model', 'type'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'owner_id' => Yii::t('ra', 'Owner ID'),
            'model' => Yii::t('ra', 'Model'),
            'type' => Yii::t('ra', 'Type'),
            'data_id' => Yii::t('ra', 'Data ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getData()
    {
        return $this->hasOne(IndexData::className(), ['id' => 'data_id']);
    }

    public function setData($value)
    {
        if(!$value = trim($value)) return;
        $this->data_id = IndexData::find()->select('id')->where(['value' => $value])->scalar();
        if (!$this->data_id) {
            $model = new IndexData();
            $model->value = $value;
            $model->save(false);
            $this->data_id = $model->id;
        }
    }
}
