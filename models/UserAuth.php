<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%user_auth}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $provider_attributes
 * @property string $updated_at
 * @property string $created_at
 *
 * @property User $user
 */
class UserAuth extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_auth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'provider', 'provider_id', 'provider_attributes'], 'required'],
            [['user_id'], 'integer'],
            [['provider_attributes'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['provider', 'provider_id'], 'string', 'max' => 255]
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
            'provider' => Yii::t('ra', 'Provider'),
            'provider_id' => Yii::t('ra', 'Provider ID'),
            'provider_attributes' => Yii::t('ra', 'Provider Attributes'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function add($provider, $providerId, $data)
    {
        $search = [
            'provider' => $provider,
            'provider_id' => $providerId,
        ];
        $model = self::findOne($search);
        if (!$model) $model = new self($search);
        $model->setAttributes([
            'user_id' => Yii::$app->user->id ?: ($model->user_id ? $model->user_id : 1),
            'provider_attributes' => serialize($data),
        ]);
        $model->save(false);
    }
}
