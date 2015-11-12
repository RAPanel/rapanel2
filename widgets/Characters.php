<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 09.04.2015
 * Time: 16:03
 */

namespace ra\admin\widgets;

use Yii;
use yii\bootstrap\Button;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\InputWidget;

class Characters extends InputWidget
{
    /**
     * @return string
     */
    public function run()
    {
        $result = '';

        if (is_null($this->value)) $this->value = $this->model->{$this->attribute};

        $result .= Html::beginTag('div', ['class' => 'characterList']);

        foreach ($this->model->existCharacters as $data) {
            $key = null;
            foreach ($this->value as $i => $row)
                if ($row->character_id == $data->id) $key = $i;
            if (is_null($key)) $key = count($this->value) + $data->id;
            $result .= $this->render('/table/_character', ['key' => $key, 'data' => $data, 'model' => $this->model, 'attribute' => $this->attribute]);
        }

        $result .= Html::endTag('div');

        $result .= Button::widget([
            'label' => 'добавить характеристику',
            'options' => [
                'type' => 'button',
                'class' => 'btn-info',
                'data-toggle' => 'modal',
                'data-target' => '#addCharacter',
                'data-remote' => Url::to(['character/create', 'module_id' => $this->model->module_id, 'page_id' => $this->model->id, 'filter' => $this->model->is_category]),
            ],
        ]);

        $html = <<<HTML
<div id="addCharacter" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content" style="padding: 15px 30px">
            <h2>Данные загружаются...</h2>
        </div>
    </div>
</div>
HTML;
        $js = <<<JS
$('#addCharacter').on('submit', 'form', function(){
    var el = $(this);
    $.post(el.attr('action'), el.serializeArray(), function(data){
        $(data).appendTo('.characterList');
        $('#addCharacter').modal('hide');
        $('#addCharacter form').trigger('reset');
    });
    return false;
});
JS;

        $this->getView()->registerJs('$(\'body\').append(\'' . str_replace(["\r", "\n"], '', $html) . '\');');
        $this->getView()->registerJs($js);

        return $result;
    }
}