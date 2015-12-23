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
use yii\helpers\Url;
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
        $deleteAction = Url::to(['delete', 'type' => 'photo', 'id' => '']);
        $js = <<<JS
$('.photoWrapper').on('click', '.remove', function(){
    var block = $(this).parents('.image');
    $.post('{$deleteAction}'+block.attr('id').replace('photo', ''));
    block.remove();
    return false;
});
$( ".photoWrapper" ).sortable();
$( ".photoWrapper" ).disableSelection();
$( ".photoWrapper" ).on( "sortstop", function( event, ui ) {
$('.photoWrapper .image').each(function(key, el){
    $('input[name$="[sort_id]"]', el).val(key);
});
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

        ini_set('memory_limit', '256M');
        foreach ($this->value as $index => $data)
            $result .= preg_replace('#[\r\n\s]+#', ' ', $this->render('/table/_image', compact('data', 'index')));

        $result .= Html::endTag('div');

        $result .= parent::run();

        return $result;
    }

}