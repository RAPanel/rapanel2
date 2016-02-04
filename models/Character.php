<?php

namespace ra\admin\models;

use ra\admin\behaviors\RelationSaveBehavior;
use ra\admin\helpers\Text;
use ra\admin\traits\SerializeAttribute;
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
    use SerializeAttribute;
    public $serializeAttributes = ['module', 'filter', 'list'];
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
            [['type', 'url'], 'string'],
            [['multi'], 'integer'],
            [['characterShows'], 'safe'],
            [$this->serializeAttributes, 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'url' => Yii::t('ra', 'Url'),
            'type' => Yii::t('ra', 'Type'),
            'multi' => Yii::t('ra', 'Multi'),
            'data' => Yii::t('ra', 'Data'),
            'name' => Yii::t('ra', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacterReferences()
    {
        return $this->hasMany(CharacterReference::className(), ['character_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferences()
    {
        return $this->hasMany(Reference::className(), ['id' => 'reference_id'])->viaTable('{{%character_reference}}', ['character_id' => 'id']);
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

    public function behaviors()
    {
        return [
            [
                'class' => RelationSaveBehavior::className(),
                'relations' => [
                    'characterShows',
                ]
            ],
        ];
    }

    public function getName()
    {
        if (!$this->_name)
            $this->_name = $this->url ? Yii::t('app\character', Inflector::camel2words($this->url)) : Inflector::camel2words($this->url);
        return $this->_name;
    }

    public function setName($value)
    {
        if (!$value) return;
        $this->on(self::EVENT_AFTER_INSERT, function ($event) {
            Message::add('app\character', Inflector::camel2words($event->sender->url), Yii::$app->language, $event->data);
        }, $value);
        $this->_name = $value;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if (!$this->url && $this->_name) $this->url = Text::translate($this->_name);

        if (strlen($this->url) > 32) $this->url = substr($this->url, 0, 30);

        if ($this->isNewRecord && ($model = self::findOne(['url' => $this->url]))) {
            $this->setAttributes($model->attributes, false);
            $this->setIsNewRecord(false);
        }
        return parent::save($runValidation, $attributeNames);
    }
}
