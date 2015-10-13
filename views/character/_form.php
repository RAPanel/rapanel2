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
        $('.dataField').hide().filter('.' + $(e).val()).show();
        if($(e).val() == 'extend'){
        }
        console.log($(e).val());
    }
</script>

<div class="character-form">

    <?php $form = ActiveForm::begin(['id' => 'addCharacterForm']); ?>

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
        <?= $form->field($model, 'data')->textarea() ?>
    </div>

    <div class="dataField extend row" style="display: none">
        <div class="col-md-4">
        <?= $form->field($model, 'data[module]')->dropDownList(RA::module(null, 'name'), ['prompt'=>'Выберите модуль'])->label('Модуль') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'data[module][filter][is_category]')->dropDownList(['все', 'товар', 'категория'])->label('Тип') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'data[module][filter][status]')->dropDownList(['все', 'скрыт', 'видим'])->label('Статус') ?>
        </div>
    </div>

    <div class="dataField dropdown" style="display: none">
        <?= $form->field($model, 'data[module]')->dropDownList(RA::module(null, 'name'), ['prompt'=>'Выберите модуль'])->label('Модуль') ?>
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
