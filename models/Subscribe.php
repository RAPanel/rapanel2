<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%subscribe}}".
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property resource $data
 * @property string $updated_at
 * @property string $created_at
 */
class Subscribe extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscribe}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['data'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'name' => Yii::t('ra', 'Name'),
            'email' => Yii::t('ra', 'Email'),
            'data' => Yii::t('ra', 'Data'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
        ];
    }
}
