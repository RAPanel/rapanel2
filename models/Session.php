<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%session}}".
 *
 * @property string $id
 * @property integer $expire
 * @property resource $data
 */
class Session extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%session}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['expire'], 'integer'],
            [['data'], 'string'],
            [['id'], 'string', 'max' => 40]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra/model', 'ID'),
            'expire' => Yii::t('ra/model', 'Expire'),
            'data' => Yii::t('ra/model', 'Data'),
        ];
    }
}
