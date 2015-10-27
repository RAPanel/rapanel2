<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 16.04.2015
 * Time: 18:45
 */

namespace ra\admin\widgets;


use Yii;
use yii\helpers\Html;
use yii\web\JsExpression;

class PhotoUpload extends DropZone
{
    public $types = null;

    public function init()
    {
        $event = <<<JS
function(file, response) {
    $(response).appendTo('.photoWrapper');
}
JS;
        $this->clientEvents['success'] = new JsExpression($event);
        $js = <<<JS
$('.photoWrapper').on('click', '.close', function(){
    var block = $(this).parents('.image');
    $.post('/rapanel/table/delete?id='+block.attr('id').replace('photo', '') + '&type=photo');
    $(this).parents('.image').remove();
    return false;
});
JS;
        $this->getView()->registerJs($js);
        parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        $result = Html::beginTag('div', ['class' => 'row photoWrapper']);

        if (is_null($this->value))
            $this->value = $this->model->{$this->attribute};

        foreach ($this->value as $index => $data)
            $result .= $this->render('/table/_image', compact('data', 'index'));

        $result .= Html::endTag('div');

        $result .= parent::run();

        return $result;
    }

}