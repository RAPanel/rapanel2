<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%cart}}".
 *
 * @property string $session_id
 * @property string $status
 * @property string $item_id
 * @property integer $order_id
 * @property double $price
 * @property double $quantity
 * @property resource $data
 * @property string $updated_at
 * @property string $created_at
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cart}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['session_id', 'status', 'item_id', 'order_id', 'data'], 'required'],
            [['status', 'order_id'], 'integer'],
            [['price', 'quantity'], 'number'],
            [['data'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['session_id', 'item_id'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'session_id' => Yii::t('ra/model', 'Session ID'),
            'status' => Yii::t('ra/model', 'Status'),
            'item_id' => Yii::t('ra/model', 'Item ID'),
            'order_id' => Yii::t('ra/model', 'Order ID'),
            'price' => Yii::t('ra/model', 'Price'),
            'quantity' => Yii::t('ra/model', 'Quantity'),
            'data' => Yii::t('ra/model', 'Data'),
            'updated_at' => Yii::t('ra/model', 'Updated At'),
            'created_at' => Yii::t('ra/model', 'Created At'),
        ];
    }

    public function afterFind()
    {
        $this->data = unserialize($this->data);
        parent::afterFind();
    }

    public function beforeSave($insert)
    {
        if ($this->data && is_object($this->data))
            $this->data = serialize($this->data);
        return parent::beforeSave($insert);
    }
}
