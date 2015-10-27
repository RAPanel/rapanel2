<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 09.04.2015
 * Time: 15:44
 */

namespace ra\admin\widgets;


use ra\admin\assets\TagsInputAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class Tags extends InputWidget
{
    public function run()
    {
        TagsInputAsset::register($this->getView());
        $this->getView()->registerJs('$(".tags").tagsInput(' . Json::encode([
                'defaultText' => 'добавить тег',
                'width' => 'auto',
            ]) . ');');
        return Html::activeInput('text', $this->model, $this->attribute, ['class' => 'tags']);
    }

}