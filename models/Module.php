<?php

namespace app\admin\models;

use app\behaviors\SettingsBehavior;
use Yii;

/**
 * This is the model class for table "{{%module}}".
 *
 * @property string $id
 * @property string $url
 * @property string $name
 * @property string $class
 * @property string $created_at
 *
 * @property CharacterShow[] $characterShows
 * @property ModuleSettings[] $moduleSettings
 * @property Page[] $pages
 */
class Module extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'name', 'class'], 'required'],
            [['settings'], 'safe'],
            [['created_at'], 'safe'],
            [['url'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 64],
            [['class'], 'string', 'max' => 255]
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
            'name' => Yii::t('rere.model', 'Name'),
            'class' => Yii::t('rere.model', 'Class'),
            'created_at' => Yii::t('rere.model', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacterShows()
    {
        return $this->hasMany(CharacterShow::className(), ['module_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModuleSettings()
    {
        return $this->hasMany(ModuleSettings::className(), ['module_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasMany(Page::className(), ['module_id' => 'id']);
    }

    public function behaviors()
    {
        return [
            'settings' => [
                'class' => SettingsBehavior::className(),
                'relationName' => 'moduleSettings',

            ],
        ];
    }

    public function getRootId()
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = $this->class;
        if (!$rootId = $model::find()->select('id')->where(['module_id' => $this->id, 'lft' => 1, 'level' => 0])->andWhere('rgt>lft')->scalar()) {
            /** @var $root \app\models\Page */
            \Yii::$app->db->createCommand()->insert($model::tableName(), [
                'id' => $this->id,
                'is_category' => 1,
                'status' => 2,
                'module_id' => $this->id,
                'name' => $this->name,
                'lft' => 1,
                'rgt' => 2,
                'url' => empty($this->settings['controller']) ? '' : '/' . $this->url,
            ])->execute();
            $rootId = \Yii::$app->db->getLastInsertID();
        }
        return $rootId;
    }
}
