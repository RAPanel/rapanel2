<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.09.2015
 * Time: 13:36
 */

namespace app\admin\traits;

trait PageEdit
{
    private $_save;

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
                    $parent = $this->parent_id ? self::findOne($this->parent_id) : $this->root;
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
            'statusChange' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => 'status',
//                    self::EVENT_BEFORE_UPDATE => 'status',
                ],
                'value' => function ($event) {
                    if ($event->sender->isNewRecord && !$event->sender->status)
                        return RA::moduleSetting($event->sender->module_id, 'status');
                    return $event->sender->status;
                }
            ]
        ]);
    }
}