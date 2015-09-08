<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%page_characters}}".
 *
 * @property string $id
 * @property string $page_id
 * @property string $character_id
 * @property string $value
 *
 * @property Page $page
 * @property Character $character
 */
class PageCharacters extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_characters}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'character_id', 'value'], 'required'],
            [['page_id', 'character_id'], 'integer'],
            [['value'], 'string'],
            [['page_id', 'character_id'], 'unique', 'targetAttribute' => ['page_id', 'character_id'], 'message' => 'The combination of Page ID and Character ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rere.model', 'ID'),
            'page_id' => Yii::t('rere.model', 'Page ID'),
            'character_id' => Yii::t('rere.model', 'Character ID'),
            'value' => Yii::t('rere.model', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'page_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacter()
    {
        return $this->hasOne(Character::className(), ['id' => 'character_id']);
    }
}
