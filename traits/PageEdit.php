<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.09.2015
 * Time: 13:36
 */

namespace ra\admin\traits;

use creocoder\nestedsets\NestedSetsBehavior;
use ra\admin\behaviors\PageHasManyBehavior;
use ra\admin\helpers\RA;
use ra\admin\models\Page;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

trait PageEdit
{
    private $_save;
    private $_attached;

    public function afterFind()
    {
        if ($this->is_category || RA::moduleSetting($this->module_id, 'hasChild'))
            $this->addBehavior('tree');
        parent::afterFind();
    }

    public function addBehavior($name)
    {
        $list = [
            'hasMany' => PageHasManyBehavior::className(),
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

        if (isset($list[$name]))
            $this->attachBehavior($name, $list[$name]);
    }

    public function setCharacters($value)
    {
        $data = [];
        foreach ($value as $key => $val) {
            $data[] = [
                'character_id' => RA::character($key),
                'value' => $val,
            ];
        }
        $this->setPageCharacters($data);
    }

    public function setPageCharacters($value)
    {
        $this->addBehavior('hasMany');
        $this->set($value, 'pageCharacters');
    }

    public function setPageData($data)
    {
        $this->addBehavior('hasMany');
        $model = $this->pageData;
        if (!$model && ($class = $this->getPageData()->modelClass)) {
            $model = new $class;
            $model->page_id = $this->id;
        }
        $model->setAttributes($data);
        if ($this->isNewRecord) $this->on(self::EVENT_AFTER_INSERT, function ($event) {
            $event->data->setAttributes(['page_id' => $event->sender->id]);
            $event->data->save(false);
        }, $model);
        else $model->save(false);
    }

    public function setPhotos($value)
    {
        $this->addBehavior('hasMany');
        $this->set($value, 'photos');
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $this->addBehavior('sluggable');
        $this->addBehavior('timestamp');
        $this->addBehavior('statusChange');

        /** @var $this NestedSetsBehavior|self|Page */
        if ($this->_save !== true && $this->getBehavior('tree') && ($this->isNewRecord || $this->isAttributeChanged('parent_id', false))) {
            $this->_save = true;
            $parent = $this->parent_id ? Page::findOne($this->parent_id) : $this->root;
            if ($this->id != $this->root->id) {
                if (!$this->parent_id)
                    $this->parent_id = $this->root->id;
                return $this->appendTo($parent, $runValidation, $attributeNames);
            }
        }

        return parent::save($runValidation, $attributeNames);
    }
}