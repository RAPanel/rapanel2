<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.09.2015
 * Time: 13:36
 */

namespace ra\admin\traits;

use creocoder\nestedsets\NestedSetsBehavior;
use ra\admin\helpers\RA;
use ra\admin\models\Page;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

trait PageEdit
{
    use AutoSet;

    private $_save;
    private $_attached;

    public function afterFind()
    {
        if (!$this instanceof ActiveRecord) return;
        if ($this->is_category || RA::moduleSetting($this->module_id, 'hasChild'))
            $this->addBehavior('tree');
        parent::afterFind();
    }

    public function addBehavior($name)
    {
        $list = [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'module_id',
                'depthAttribute' => 'level',
            ],
            'sluggable' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'slugAttribute' => 'url',
                'immutable' => true,
                'ensureUnique' => true,
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => function () {
                    return date("Y-m-d H:i:s");
                },
            ],
            'statusChange' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'status',
//                    self::EVENT_BEFORE_UPDATE => 'status',
                ],
                'value' => function ($event) {
                    if ($event->sender->isNewRecord && !$event->sender->status)
                        return RA::moduleSetting($event->sender->module_id, 'status');
                    return $event->sender->status;
                }
            ]
        ];

        if (isset($list[$name]) && $this instanceof ActiveRecord && !(bool)$this->getBehavior($name))
            $this->attachBehavior($name, $list[$name]);
    }

    public function setCharacters($values, $update = false)
    {
        if ($update) $this->_characters = $this->getCharacters(true);
        foreach ($values as $key => $value)
            $this->_characters[$key] = $value;

        $data = [];
        foreach ($this->_characters as $key => $val) {
            $data[] = [
                'character_id' => RA::character($key),
                'value' => $val,
            ];
        }
        $this->setPageCharacters($data);
    }

    public function setPageCharacters($value)
    {
        $this->setRelation('pageCharacters', $value, ['pk' => 'character_id']);
    }

    public function setPageData($value)
    {
        $this->setRelation('pageData', $value);
    }

    public function setPhotos($value)
    {
        $this->setRelation('photos', $value);
    }

    /**
     * @param $this $this NestedSetsBehavior|self|Page
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        /** @var $this NestedSetsBehavior|self|Page */
        if (!$this instanceof ActiveRecord) return false;
        if ($this->is_category || /*($this->isNewRecord && RA::moduleSetting($this->module_id, 'hasCategory')) ||*/
            RA::moduleSetting($this->module_id, 'hasChild')
        )
            $this->addBehavior('tree');
        elseif ($this->isNewRecord && !$this->lft && RA::moduleSetting($this->module_id, 'sort')) {
            $this->lft = $this::find()->where(['module_id' => $this->module_id])->select('MAX(lft)')->scalar();
        }

        if (($this->isNewRecord || $this->isAttributeChanged('parent_id', false)) && $this->parent_id && $this->parent)
            $this->level = $this->parent->level + 1;

        if (!$this->about) $this->about = '';

        foreach (['about' => 2560, 'name' => 255, 'url' => 255] as $attribute => $length)
            if (mb_strlen($this->{$attribute}, 'utf8')) $this->{$attribute} = mb_substr($this->{$attribute}, 0, $length, 'utf8');

        $this->addBehavior('sluggable');
        $this->addBehavior('timestamp');
        $this->addBehavior('statusChange');

        if ($this->_save !== true && $this->getBehavior('tree') && ($this->isNewRecord || $this->isAttributeChanged('parent_id', false))) {
            $this->_save = true;
            $parent = $this->parent_id ? Page::findOne($this->parent_id) : $this->root;
            if (empty($this->root) || $this->id != $this->root->id) {
                if (!$this->parent_id && $this->root)
                    $this->parent_id = $this->root->id;
                return $this->appendTo($parent, $runValidation, $attributeNames);
            }
        }

        return parent::save($runValidation, $attributeNames);
    }

    public function __get($name)
    {
        if (strpos($name, '.')) {
            $data = $this;
            foreach (explode('.', $name) as $key)
                $data = isset($data[$key]) ? $data[$key] : null;
            return $data;
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (strpos($name, '.')) {
            $keys = explode('.', $name);
            $data = array_shift($keys);
            foreach (array_reverse($keys) as $key)
                $value = [$key => $value];
            $this->{$data} = $value;

        } else
            parent::__set($name, $value);
    }
}