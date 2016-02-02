<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%user_key}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $key_value
 * @property string $create_time
 * @property string $consume_time
 * @property string $expire_time
 *
 * @property User $user
 */
class UserKey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_key}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'key_value'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['create_time', 'consume_time', 'expire_time'], 'safe'],
            [['key_value'], 'string', 'max' => 255],
            [['key_value'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'user_id' => Yii::t('ra', 'User ID'),
            'type' => Yii::t('ra', 'Type'),
            'key_value' => Yii::t('ra', 'Key Value'),
            'create_time' => Yii::t('ra', 'Create Time'),
            'consume_time' => Yii::t('ra', 'Consume Time'),
            'expire_time' => Yii::t('ra', 'Expire Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function behaviors()
    {
        return [
            [
                'class' => '\yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'create_time',
            ]
        ];
    }
}
