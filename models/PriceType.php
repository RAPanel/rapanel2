<?php

namespace ra\admin\models;

use Yii;

/**
 * This is the model class for table "{{%price_type}}".
 *
 * @property string $id
 * @property string $name
 * @property string $currency
 */
class PriceType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%price_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'currency'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 8],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'name' => Yii::t('ra', 'Name'),
            'currency' => Yii::t('ra', 'Currency'),
        ];
    }
}
