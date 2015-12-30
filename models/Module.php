<?php

namespace ra\admin\models;

use ra\admin\traits\AutoSet;
use Yii;
use yii\helpers\ArrayHelper;

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
    use AutoSet;

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
            [['settings', 'moduleCharacters'], 'safe'],
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
            'id' => Yii::t('ra', 'ID'),
            'url' => Yii::t('ra', 'Url'),
            'name' => Yii::t('ra', 'Name'),
            'class' => Yii::t('ra', 'Class'),
            'created_at' => Yii::t('ra', 'Created At'),
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

    public function getRootId()
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = $this->class;
        if (!$rootId = $model::find()->select('id')->where(['module_id' => $this->id, 'lft' => 1, 'level' => 0])->andWhere('rgt>lft')->scalar()) {
            /** @var $root \ra\admin\models\Page */
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

    public function getModuleCharacters()
    {
        return ArrayHelper::map($this->characterShows, 'character_id', 'character_id');
    }

    public function setModuleCharacters($values)
    {
        $data = [];
        if (!empty($values)) foreach ($values as $value)
            $data[] = ['character_id' => $value];
        $this->setRelation('characterShows', $data, ['pk' => 'character_id', 'validation'=>false]);
    }

    public function getSettings()
    {
        return ArrayHelper::map($this->moduleSettings, 'url', 'value');
    }

    public function setSettings($values)
    {
        $data = [];
        if (!empty($values)) foreach ($values as $url => $list)
            foreach ((array)$list as $sort => $value)
                $data[] = compact('sort', 'url', 'value');
        $this->setRelation('moduleSettings', $data, ['pk' => 'url', 'validation'=>false]);
    }

    public function fixTree()
    {
        Page::deleteAll(['module_id' => $this->id, 'status' => 9]);
        $data = Page::find()
            ->where(['and', ['module_id' => $this->id], ['>', 'lft', 0], ['>', 'rgt', 0]])
            ->select(['id', 'parent_id', 'lft', 'rgt', 'level'])
            ->orderBy('lft, id DESC')
            ->asArray()->all();
        $items = array();
        foreach ($data as $row) {
            $items[(int)$row['parent_id']][] = $row;
        }
        $lft = 1;
        $index = function ($parent_id, $level) use ($items, &$lft, &$index) {
            if (!empty($items[$parent_id]))
                foreach ($items[$parent_id] as $row) {
                    $update['level'] = $level;
                    $update['lft'] = $lft++;
                    call_user_func($index, $row['id'], $level + 1);
                    $update['rgt'] = $lft++;
                    Page::updateAll($update, ['id' => $row['id']]);
                }
        };
        $transaction = Yii::$app->db->beginTransaction();
        call_user_func($index, 0, 0);
        $transaction->commit();
    }
}
