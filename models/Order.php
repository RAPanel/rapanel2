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
    public $serializeAttributes = ['name', 'phone', 'email', 'address', 'comment'];
    private $_data = [];

    public function __set($name, $value)
    {
        if (in_array($name, $this->serializeAttributes)) {
            if ($this->_data === false) $this->_data = unserialize($this->_data);
            $this->_data[$name] = $value;
            $this->on($this->isNewRecord ? self::EVENT_AFTER_INSERT : self::EVENT_AFTER_UPDATE, function ($event) {
                if ($event->sender->_data) {
                    $event->sender->data = serialize($event->sender->_data);
                    $event->sender->_data = false;
                }
            });
            return;
        }
        parent::__set($name, $value);
    }

    public function __get($name)
    {
        if (in_array($name, $this->serializeAttributes)) {
            if ($this->_data === false) $this->_data = unserialize($this->_data);
            return isset($this->_data[$name]) ? $this->_data[$name] : null;
        }
        return parent::__get($name);
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
            'id' => Yii::t('rere.model', 'ID'),
            'status_id' => Yii::t('rere.model', 'Status ID'),
            'is_paied' => Yii::t('rere.model', 'Is Paied'),
            'session_id' => Yii::t('rere.model', 'Session ID'),
            'delivery_id' => 'Адрес доставки',
            'pay_id' => Yii::t('rere.model', 'Pay ID'),
            'data' => Yii::t('rere.model', 'Data'),
            'updated_at' => Yii::t('rere.model', 'Updated At'),
            'created_at' => Yii::t('rere.model', 'Created At'),

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
}
