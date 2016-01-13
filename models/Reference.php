<?php

namespace ra\admin\models;

use ra\admin\traits\AutoSet;
use Yii;

/**
 * This is the model class for table "{{%reference}}".
 *
 * @property string $id
 * @property string $value
 *
 * @property CharacterReference[] $characterReferences
 * @property Character[] $characters
 */
class Reference extends \yii\db\ActiveRecord
{
    use AutoSet;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reference}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            [['value'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'value' => Yii::t('ra', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacters()
    {
        return $this->hasMany(Character::className(), ['id' => 'character_id'])->viaTable('{{%character_reference}}', ['reference_id' => 'id']);
    }

    public function setCharacter_id($value)
    {
        $add = function ($event) {
            $model = new CharacterReference();
            $model->setAttributes(['character_id' => $event->data, 'reference_id' => $event->sender->id]);
            $model->insert();
        };
        if ($this->isNewRecord) $this->on(self::EVENT_AFTER_INSERT, $add, $value);
        elseif (!$this->getCharacterReferences()->where(['character_id' => $value])->exists())
            $this->on(self::EVENT_AFTER_UPDATE, $add, $value);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacterReferences()
    {
        return $this->hasMany(CharacterReference::className(), ['reference_id' => 'id']);
    }
}
