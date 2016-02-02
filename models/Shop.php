<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 03.09.2015
 * Time: 15:03
 */

namespace ra\admin\models;


use ra\admin\traits\CartItem;
use Yii;
use yii\helpers\FileHelper;

class Shop extends Page
{
    use CartItem;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['parent', 'characters', 'photo'], 'safe'];
        return $rules;
    }

    public function setParent($value)
    {
        $value['module_id'] = $this->module_id;
        $value['is_category'] = 1;
        $find = $this->parent_id ?: self::find()->select('id')->where([
            'name' => $value['name'],
            'module_id' => $value['module_id'],
            'is_category' => 1,
        ])->scalar();
        if ($find) {
            $this->parent_id = $find;
            return;
        }

        $model = new self();
        $model->setAttributes(array_reverse($value));
        if (!$model->save()) {
            $this->on(self::EVENT_BEFORE_VALIDATE, function ($event) {
                $event->sender->addError($event->data['attribute'], print_r($event->data['errors'], 1));
                return false;
            }, ['errors' => $model->errors, 'attribute' => 'parent_id']);
            return;
        }
        $dir = Yii::getAlias('@app/../brands/') . $model->url . '.jpg';
        if (file_exists($dir)) Photo::add($dir, $model->url, $model->id, $model::className());
        $this->parent_id = $model->id;
        return;
    }

    public function setPhoto($value)
    {
        $add = function ($event) {
            $name = basename($event->data);
            $parse = explode('=', $name);
            $name = end($parse);

            $temp = md5($event->data) . '.' . pathinfo($name, PATHINFO_EXTENSION);
            $dir = Yii::getAlias('@runtime/uploadedFiles/');
            FileHelper::createDirectory($dir);
            if (!file_exists($dir . $temp))
                copy($event->data, $dir . $temp);
            Photo::add($dir . $temp, $event->data, $event->sender->id, self::tableName());
        };
        $this->on($this->id && empty($this->photos) ? self::EVENT_AFTER_UPDATE : self::EVENT_AFTER_INSERT, $add, $value);
    }

    public function getQuantity()
    {
        return $this->pagePrice ? $this->pagePrice->quantity : 0;
    }

    public function getPrice($format = true)
    {
        return $this->pagePrice ? ($format ? Yii::$app->formatter->asCurrency($this->pagePrice->value) : $this->pagePrice->value) : 0;
    }

    public function getUnit()
    {
        return 'шт';
    }

    public function getOldPrice()
    {
        return $this->getCharacters('old-price');
    }

    public function getDeliveryDate()
    {
        return null;
    }

    public function getExist()
    {
        return $this->pagePrice ? (bool)$this->pagePrice->count : false;
    }

}