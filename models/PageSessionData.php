<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%page_session_data}}".
 *
 * @property string $page_id
 * @property string $session
 * @property string $type
 * @property string $value
 * @property string $last_visit
 */
class PageSessionData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_session_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'session', 'type'], 'required'],
            [['page_id'], 'integer'],
            [['last_visit'], 'safe'],
            [['session'], 'string', 'max' => 40],
            [['type'], 'string', 'max' => 8],
            [['value'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => Yii::t('rere.model', 'Page ID'),
            'session' => Yii::t('rere.model', 'Session'),
            'type' => Yii::t('rere.model', 'Type'),
            'value' => Yii::t('rere.model', 'Value'),
            'last_visit' => Yii::t('rere.model', 'Last Visit'),
        ];
    }
}
