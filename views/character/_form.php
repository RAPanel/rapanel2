<?php

use app\admin\helpers\RA;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Character */
/* @var $form yii\widgets\ActiveForm */


?>

<script>
    function characterGetter(e) {
        $('.dataField').hide().find(':input').prop('disabled', 1);
        if($(e).val())
            $('.dataField.' + $(e).val()).show().find(':input').prop('disabled', 0);
    }
</script>

<div class="character-form">

    <?php $form = ActiveForm::begin(['id' => 'addCharacterForm']); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'value' => $model->getName()]) ?>

    <? if (!$model->isNewRecord) echo $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'type')->dropDownList(RA::dropDownList($model->getTableSchema()->columns['type']->enumValues), ['prompt' => Yii::t('ra/placeholder', 'Select Type'), 'onchange' => 'characterGetter(this)'])->label(false) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'multi')->checkbox() ?>
        </div>
    </div>

    <div class="dataField" style="display: none">
        <? //= $form->field($model, 'data')->textarea() ?>
    </div>

    <? $disable = !$model->type || $model->type != 'extend' ?>
    <div class="dataField extend row" <? if ($model->type != 'extend') echo 'style="display: none"' ?>>
        <div class="col-md-4">
            <?= $form->field($model, 'module')->dropDownList(RA::module(null, 'name'), ['prompt' => 'Выберите модуль', 'disabled' => $disable])->label('Модуль') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'filter[is_category]')->dropDownList(['все', 'товар', 'категория'], ['disabled' => $disable])->label('Тип') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'filter[status]')->dropDownList(['все', 'скрыт', 'видим'], ['disabled' => $disable])->label('Статус') ?>
        </div>
    </div>

    <div class="dataField dropdown" style="display: none">
    </div>

    <? if ($model->isNewRecord): ?>
        <? if ($value = Yii::$app->request->get('page_id'))
            echo $form->field($model, 'characterShows[0][page_id]')->hiddenInput(compact('value'))->label(false) ?>
        <? if ($value = Yii::$app->request->get('module_id'))
            echo $form->field($model, 'characterShows[0][module_id]')->hiddenInput(compact('value'))->label(false) ?>
        <? if ($value = Yii::$app->request->get('filter'))
            echo $form->field($model, 'characterShows[0][filter]')->hiddenInput(compact('value'))->label(false) ?>
    <? endif ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('ra/view', 'Create') : Yii::t('ra/view', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
