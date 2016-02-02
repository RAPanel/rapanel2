<?php

namespace ra\admin\models;

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
 * @property string $updated_at
 *
 * @property Page $page
 * @property PriceType $type
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
            [['updated_at'], 'safe'],
            [['unit'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'page_id' => Yii::t('ra', 'Page ID'),
            'type_id' => Yii::t('ra', 'Type ID'),
            'unit' => Yii::t('ra', 'Unit'),
            'value' => Yii::t('ra', 'Value'),
            'count' => Yii::t('ra', 'Count'),
            'updated_at' => Yii::t('ra', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'page_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(PriceType::className(), ['id' => 'type_id']);
    }
}
