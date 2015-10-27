<?php

namespace ra\admin\models;

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
    public static function add($category, $message, $language, $translation = '')
    {
        if (!$message) return $message;
        if (!$id = Message::find()->select('id')->where(compact('category', 'message'))->scalar()) {
            Yii::$app->db->createCommand()->insert(Message::tableName(), [
                'category' => $category,
                'message' => $message,
            ])->execute();
            $id = Yii::$app->db->getLastInsertID();
        }
        if (!MessageTranslate::find()->where(compact('id', 'language'))->exists()) {
            if (!$translation) {
                $translate = Yii::$app->translation->translate(Yii::$app->sourceLanguage, $language, $message);
                if (isset($translate['code']) && $translate['code'] == 200)
                    $translation = current($translate['text']);
            }
            Yii::$app->db->createCommand()->insert(MessageTranslate::tableName(), [
                'id' => $id,
                'language' => $language,
                'translation' => $translation,
            ])->execute();
        }
        if ($translation) return $translation;
        return false;
    }

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
            'id' => Yii::t('ra', 'ID'),
            'category' => Yii::t('ra', 'Category'),
            'message' => Yii::t('ra', 'Message'),
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
        return $this->hasOne(MessageTranslate::className(), ['id' => 'id'])->where(['language' => Yii::$app->language]);
    }
}
