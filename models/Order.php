<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $id
 * @property integer $status_id
 * @property integer $is_paied
 * @property string $session_id
 * @property string $delivery_id
 * @property string $pay_id
 * @property resource $data
 * @property string $updated_at
 * @property string $created_at
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status_id', 'is_paied', 'session_id', 'delivery_id', 'pay_id', 'data'], 'required'],
            [['status_id', 'is_paied', 'delivery_id', 'pay_id'], 'integer'],
            [['data'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['session_id'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rere.model', 'ID'),
            'status_id' => Yii::t('rere.model', 'Status ID'),
            'is_paied' => Yii::t('rere.model', 'Is Paied'),
            'session_id' => Yii::t('rere.model', 'Session ID'),
            'delivery_id' => Yii::t('rere.model', 'Delivery ID'),
            'pay_id' => Yii::t('rere.model', 'Pay ID'),
            'data' => Yii::t('rere.model', 'Data'),
            'updated_at' => Yii::t('rere.model', 'Updated At'),
            'created_at' => Yii::t('rere.model', 'Created At'),
        ];
    }
}
