<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 16.04.2015
 * Time: 18:45
 */

namespace ra\admin\widgets;


use ra\admin\assets\CropperAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

class PhotoUpload extends DropZone
{
    public $types = null;
    public $crop;

    public function init()
    {
        $event = <<<JS
function(file, response) {
    var list = {};
    var img = $(response);
    var select = img.find('select[name$="[type]"] option').each(function(){
       list[this.value] = 0;
    }).parent('select');
    $('.photoWrapper select[name$="[type]"]').each(function(){
        list[this.value] = 1;
    });
    var value, n=0;
    for(var i in list){
        if(!list[i] && !value ||  Object.keys(list).length - 1 == n++)
            value = i;
    }
    if(select.val()!=value) select.val(value).trigger('change');
    img.appendTo('.photoWrapper');
}
JS;
        $this->clientEvents['success'] = new JsExpression($event);
        $deleteAction = Url::to(['delete', 'type' => 'photo', 'id' => '']);
        $js = <<<JS
$('.photoWrapper').on('click', '.remove', function(){
    var block = $(this).parents('.modal').modal('hide').on('hidden.bs.modal', function (e) {
        block.remove();
    }).parents('.image').hide();
    $.post('{$deleteAction}'+block.attr('id').replace('photo', ''));
    return false;
})
.on('show.bs.modal', '.modal', function (e) {
    var block = $('.dataSerialized', this);
    if(!block.length) return;
    var modal = $('.imageTemplate').html();
    var data = JSON.parse(block.text());
    for(var i in data){
        modal = modal.replace(new RegExp('{{' + i + '}}', 'g'), data[i]);
    }
    block.after(modal).remove();
    $('[name="Shop[photos]['+data.index+'][type]"]').val(data.type);
})
.sortable({handle: 'a'})
.disableSelection()
.on( "sortstop", function( event, ui ) {
$('.photoWrapper .image').each(function(key, el){
    $('input[name$="[sort_id]"]', el).val(key);
});
});
JS;
        $this->getView()->registerJs($js);
        if ($this->crop) $this->addCrop();
        parent::init();
    }

    public function addCrop()
    {
        $js = <<<JS
$('.photoWrapper').on('show.bs.modal', '.modal', function (e) {
    var modal = this;
    var data={};
    $('input[name*="[cropParams]"]', modal).each(function() {
      data[this.name.match(/\[cropParams\]\[(\w+)\]/)[1]] = parseInt(this.value)
    });
    $('img', modal).load(function(){
    setTimeout(function(){
        $('img', modal).cropper({
          autoCropArea: 1,
          aspectRatio: {$this->crop},
          data: data,
          crop: function(e) {
              $.each(e, function(key, value) {
                $('input[name*="[cropParams]['+key+']"]', modal).val(Math.round(value));
              });
          }
        });
    },200);
    });
});
JS;
        CropperAsset::register($this->getView());
        $this->getView()->registerJs($js);
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        $result = Html::tag('div', $this->render('/table/_imageTemplate', ['model' => $this->model]), ['class' => 'hide imageTemplate']);

        $result .= Html::beginTag('div', ['class' => 'row photoWrapper']);


        if (is_null($this->value))
            $this->value = $this->model->{$this->attribute};

        foreach ($this->value as $index => $data)
            $result .= preg_replace('#[\r\n\s]+#', ' ', $this->render('/table/_image', compact('data', 'index')));

        $result .= Html::endTag('div');

        $result .= parent::run();

        return $result;
    }

}