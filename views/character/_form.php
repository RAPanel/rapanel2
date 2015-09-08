<?php

use app\admin\helpers\RA;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Character */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="character-form">

    <?php $form = ActiveForm::begin(['id'=>'addCharacterForm']); ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'type')->dropDownList(RA::dropDownList($model->getTableSchema()->columns['type']->enumValues, 'rere.dropDown'), ['prompt' => Yii::t('rere.placeholder', 'Select ' . $model->getAttributeLabel('type'))])->label(false) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'multi')->checkbox() ?>
        </div>
    </div>

    <?= $form->field($model, 'data')->textarea() ?>

    <? if ($model->isNewRecord): ?>
        <? if ($value = Yii::$app->request->get('page_id'))
            echo $form->field($model, 'characterShows[0][page_id]')->hiddenInput(compact('value'))->label(false) ?>
        <? if ($value = Yii::$app->request->get('module_id'))
            echo $form->field($model, 'characterShows[0][module_id]')->hiddenInput(compact('value'))->label(false) ?>
        <? if ($value = Yii::$app->request->get('filter'))
            echo $form->field($model, 'characterShows[0][filter]')->hiddenInput(compact('value'))->label(false) ?>
    <? endif ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rere.view', 'Create') : Yii::t('rere.view', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
