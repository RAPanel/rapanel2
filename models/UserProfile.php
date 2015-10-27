<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%user_profile}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $updated_at
 * @property string $created_at
 * @property string $full_name
 * @property string $city
 * @property string $vk
 * @property string $fb
 * @property string $ig
 * @property string $tw
 * @property string $options
 *
 * @property User $user
 */
class UserProfile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['full_name'], 'string', 'max' => 255],
            [['city', 'vk', 'fb', 'ig', 'tw', 'options'], 'string', 'max' => 64]
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
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
            'full_name' => Yii::t('ra', 'Full Name'),
            'city' => Yii::t('ra', 'City'),
            'vk' => Yii::t('ra', 'Vk'),
            'fb' => Yii::t('ra', 'Fb'),
            'ig' => Yii::t('ra', 'Ig'),
            'tw' => Yii::t('ra', 'Tw'),
            'options' => Yii::t('ra', 'Options'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
