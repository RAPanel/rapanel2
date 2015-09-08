<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property integer $id
 * @property string $category
 * @property string $message
 *
 * @property MessageTranslate[] $messageTranslates
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rere.model', 'ID'),
            'category' => Yii::t('rere.model', 'Category'),
            'message' => Yii::t('rere.model', 'Message'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageTranslates()
    {
        return $this->hasMany(MessageTranslate::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageTranslate()
    {
        return $this->hasOne(MessageTranslate::className(), ['id' => 'id'])->where(['language'=>Yii::$app->language]);
    }
}
