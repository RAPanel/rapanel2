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
        $this->doEditable();
        $this->set($value, 'pageCharacters');
    }

    public function doEditable()
    {
        if ($this->_attached) return;
        $this->attachBehaviors([
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
        ]);
        $this->_attached = true;
    }

    public function setPageData($data)
    {
        $this->doEditable();
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
        $this->doEditable();
        $this->set($value, 'photos');
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        /** @var $this NestedSetsBehavior|self|Page */
        $this->doEditable();
        if ($this->_save !== true && $this->is_category && ($this->isNewRecord || $this->isAttributeChanged('parent_id'))) {
            $this->_save = true;
            $parent = $this->parent_id ? Page::findOne($this->parent_id) : $this->root;
            $parent->doEditable();
            if ($this->id != $this->root->id) {
                if (!$this->parent_id)
                    $this->parent_id = $this->root->id;
                return $this->appendTo($parent, $runValidation, $attributeNames);
            }
        }
        if (!RA::moduleSetting($this->module_id, 'hasChild') && !$this->is_category) {
            $this->detachBehavior('tree');
            if ($this->isNewRecord && $this->parent_id && !$this->lft)
                $this->lft = $this::find()->select('MAX(lft)')->where(['parent_id' => $this->parent_id, 'module_id' => $this->module_id])->scalar();
        }

        return parent::save($runValidation, $attributeNames);
    }
}