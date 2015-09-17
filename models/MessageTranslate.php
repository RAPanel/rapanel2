<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%message_translate}}".
 *
 * @property integer $id
 * @property string $language
 * @property string $translation
 *
 * @property Message $id0
 */
class MessageTranslate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message_translate}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language'], 'required'],
            [['id'], 'integer'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra/model', 'ID'),
            'language' => Yii::t('ra/model', 'Language'),
            'translation' => Yii::t('ra/model', 'Translation'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(Message::className(), ['id' => 'id']);
    }
}
