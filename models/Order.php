<?php

namespace app\admin\models;

use app\admin\traits\SerializeAttribute;
use Yii;
use yii\swiftmailer\Message;

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
    use SerializeAttribute;
    public $serializeAttributes = ['name', 'phone', 'email', 'address', 'comment'];

    public function init()
    {
        $this->on(self::EVENT_AFTER_INSERT, function ($event) {
            Yii::$app->mailer->compose()
                ->setTo('semyonchick@gmail.com')
//                ->setFrom([$this->email => $this->name])
                ->setSubject('Заказ на АвтоКом')
                ->setTextBody($event->sender->id)
                ->send();
        });
        parent::init();
    }

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
            [['delivery_id'], 'required'],
            [['status_id', 'is_paied', 'delivery_id', 'pay_id'], 'integer'],
            [['data'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['session_id'], 'string', 'max' => 32],
            [['name', 'phone'], 'required'],
            [['session_id'], 'default', 'value' => Yii::$app->cart->getSessionId()],
            [['email'], 'email'],
            [$this->serializeAttributes, 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra/model', 'ID'),
            'status_id' => Yii::t('ra/model', 'Status ID'),
            'is_paied' => Yii::t('ra/model', 'Is Paied'),
            'session_id' => Yii::t('ra/model', 'Session ID'),
            'delivery_id' => Yii::t('ra/model', 'Delivery ID'),
            'delivery' => Yii::t('ra/model', 'Delivery ID'),
            'pay_id' => Yii::t('ra/model', 'Pay ID'),
            'pay' => Yii::t('ra/model', 'Pay ID'),
            'data' => Yii::t('ra/model', 'Data'),
            'updated_at' => Yii::t('ra/model', 'Updated At'),
            'created_at' => Yii::t('ra/model', 'Created At'),

            'name' => 'Имя',
            'phone' => 'Телефон',
            'email' => 'E-mail',
            'address' => 'Адрес',
            'comment' => 'Дополнительно',
        ];
    }

    public function getDeliveries()
    {
        return [
            1 => 'Самовывоз',
            5 => 'Доставка по Перми',
        ];
    }

    public function getDelivery()
    {
        return isset($this->deliveries[$this->delivery_id]) ? $this->deliveries[$this->delivery_id] : null;
    }

    public function getPais()
    {
        return [
            0 => 'Наличные',
        ];
    }

    public function getPay()
    {
        return isset($this->pais[$this->pay_id]) ? $this->pais[$this->pay_id] : null;
    }
}
