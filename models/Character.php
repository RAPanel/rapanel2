<?php

namespace app\admin\models;

use Yii;
use yii\helpers\Inflector;

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
    private $_name;

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
            [['name', 'type', 'multi'], 'required'],
            [['type', 'data'], 'string'],
            [['multi'], 'integer'],
            [['name'], 'string', 'max' => 25],
            [['characterShows'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra/model', 'ID'),
            'url' => Yii::t('ra/model', 'Url'),
            'type' => Yii::t('ra/model', 'Type'),
            'multi' => Yii::t('ra/model', 'Multi'),
            'data' => Yii::t('ra/model', 'Data'),
            'name' => Yii::t('ra/model', 'Name'),
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

    public function getName()
    {
        if ($this->_name === false)
            $this->_name = $this->url ? Yii::t('app/character', Inflector::camel2words($this->url)) : $this->url;
        return $this->_name;
    }

    public function setName($value)
    {
        if (!$value) return;
        if(!$this->url){
            $translate = Yii::$app->translation->translate(Yii::$app->language, Yii::$app->sourceLanguage, $value);
            if (isset($translate['code']) && $translate['code'] == 200) {
                $translation = current($translate['text']);
                $translation = preg_replace('#[^\w\d]#', ' ', strtolower($translation));
                $translation = preg_replace('#\s+#', '-', trim($translation));
                $this->url = $translation;
            }
        }
        if ($this->url)
            Message::add('app/character', Inflector::camel2words($this->url), Yii::$app->language, $value);
        $this->_name = $value;
    }
}
