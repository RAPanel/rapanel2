<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.09.2015
 * Time: 13:36
 */

namespace app\admin\traits;

use app\admin\behaviors\PageHasManyBehavior;
use app\admin\helpers\RA;
use app\models\Page;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

trait PageEdit
{
    private $_save;
    private $_attached;

    public function setPageCharacters($value)
    {
        $this->doEditable();
        $this->set($value, 'pageCharacters');
    }

    public function setPhotos($value)
    {
        $this->doEditable();
        $this->set($value, 'photos');
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $this->doEditable();
        if ($this->_save !== true) {
            if ($this->is_category) {
                $this->_save = true;
                /** @var $this NestedSetsBehavior|self */
                if ($this->isNewRecord || $this->isAttributeChanged('parent_id')) {
                    $parent = $this->parent_id ? Page::findOne($this->parent_id) : $this->root;
                    $parent->doEditable();
                    if ($this->id != $this->root->id) {
                        if (!$this->parent_id)
                            $this->parent_id = $this->root->id;
                        return $this->appendTo($parent, $runValidation, $attributeNames);
                    }
                }
            }
            $this->detachBehavior('tree');
        }

        return parent::save($runValidation, $attributeNames);
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
}