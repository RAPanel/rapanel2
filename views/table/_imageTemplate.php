<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.09.2015
 * Time: 0:07
 *
 * @var ra\admin\models\Page $model
 */
use ra\admin\helpers\RA;
use yii\helpers\Html;

$name = "photos[{{index}}]";

$types = array_map('trim', explode(',', RA::moduleSetting($model->module_id, 'photosTypes')));
array_unshift($types, 'main');
$types = array_diff($types, ['']);
array_unique($types);
?>


<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            {{name}}
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-7" style="max-height: 400px">
                    <img class="img-responsive" {{src}}></button>
                </div>
                <div class="col-md-5 form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-7"><?=
                            Html::activeDropDownList($model, "{$name}[type]", RA::dropDownList($types), ['class' => 'form-control col-sm-7', 'value' => '{{type}}', 'onchange'=>'$(this).parents(".image").find(".label").text(this.value)']) ?></div>
                        <div class="col-sm-5" style="line-height: 2.4">
                            <span class="width">{{width}}</span>x<span
                                class="height">{{height}}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12"><?=
                            Html::activeTextarea($model, "{$name}[about]", ['rows' => 2, 'class' => 'form-control', 'value' => '{{about}}']) ?></div>
                    </div>
                    <?= Html::button('ok', ['class' => 'btn btn-primary pull-right', 'data-dismiss' => "modal", 'aria-hidden' => "true"]) ?>
                    <?= Html::button('delete', ['class' => 'btn btn-danger remove']) ?>
                </div>
            </div>
        </div>
    </div>
</div>