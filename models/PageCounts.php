<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%page_counts}}".
 *
 * @property string $page_id
 * @property string $views
 * @property string $likes
 * @property string $comments
 */
class PageCounts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_counts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'views', 'likes', 'comments'], 'required'],
            [['page_id', 'views', 'likes', 'comments'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => Yii::t('ra/model', 'Page ID'),
            'views' => Yii::t('ra/model', 'Views'),
            'likes' => Yii::t('ra/model', 'Likes'),
            'comments' => Yii::t('ra/model', 'Comments'),
        ];
    }
}
