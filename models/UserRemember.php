<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%user_remember}}".
 *
 * @property string $user_id
 * @property string $key
 * @property string $value
 */
class UserRemember extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_remember}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'key', 'value'], 'required'],
            [['user_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['value'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('rere.model', 'User ID'),
            'key' => Yii::t('rere.model', 'Key'),
            'value' => Yii::t('rere.model', 'Value'),
        ];
    }
}
