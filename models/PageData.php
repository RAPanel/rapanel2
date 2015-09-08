<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%page_data}}".
 *
 * @property string $page_id
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $content
 * @property string $tags
 *
 * @property Page $page
 */
class PageData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'title', 'description', 'keywords', 'content', 'tags'], 'required'],
            [['page_id'], 'integer'],
            [['content', 'tags'], 'string'],
            [['title', 'description', 'keywords'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => Yii::t('rere.model', 'Page ID'),
            'title' => Yii::t('rere.model', 'Title'),
            'description' => Yii::t('rere.model', 'Description'),
            'keywords' => Yii::t('rere.model', 'Keywords'),
            'content' => Yii::t('rere.model', 'Content'),
            'tags' => Yii::t('rere.model', 'Tags'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'page_id']);
    }
}
