<?php

namespace app\admin\models;

use Yii;

/**
 * This is the model class for table "{{%page_price}}".
 *
 * @property string $id
 * @property string $page_id
 * @property string $type_id
 * @property string $unit
 * @property double $value
 * @property string $count
 * @property string $lastmod
 */
class PagePrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_price}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'type_id', 'unit', 'value', 'count'], 'required'],
            [['page_id', 'type_id', 'count'], 'integer'],
            [['value'], 'number'],
            [['lastmod'], 'safe'],
            [['unit'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rere.model', 'ID'),
            'page_id' => Yii::t('rere.model', 'Page ID'),
            'type_id' => Yii::t('rere.model', 'Type ID'),
            'unit' => Yii::t('rere.model', 'Unit'),
            'value' => Yii::t('rere.model', 'Value'),
            'count' => Yii::t('rere.model', 'Count'),
            'lastmod' => Yii::t('rere.model', 'Lastmod'),
        ];
    }
}
