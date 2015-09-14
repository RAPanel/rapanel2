<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%character}}".
 *
 * @property string $id
 * @property string $url
 * @property string $type
 * @property integer $multi
 * @property resource $data
 *
 * @property CharacterShow[] $characterShows
 * @property PageCharacters[] $pageCharacters
 */
class Character extends \yii\db\ActiveRecord
{
    use \app\admin\traits\AutoSet;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%character}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'type', 'multi'], 'required'],
            [['type', 'data'], 'string'],
            [['multi'], 'integer'],
            [['url'], 'string', 'max' => 32],
            [['characterShows'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rere.model', 'ID'),
            'url' => Yii::t('rere.model', 'Url'),
            'type' => Yii::t('rere.model', 'Type'),
            'multi' => Yii::t('rere.model', 'Multi'),
            'data' => Yii::t('rere.model', 'Data'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacterShows()
    {
        return $this->hasMany(CharacterShow::className(), ['character_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageCharacters()
    {
        return $this->hasMany(PageCharacters::className(), ['character_id' => 'id']);
    }

    public function setCharacterShows($list)
    {
        $this->setRelations('characterShows', $list);
    }
}
