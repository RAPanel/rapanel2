<?php

use app\admin\helpers\RA;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Module */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="module-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= $form->field($model, 'settings[columns]')->widget(\app\admin\widgets\ChosenSortable::className(), [
            'items' => RA::tableColumns($model->class, isset($model->settings['columns']) ? $model->settings['columns'] : []),
            'clientOptions' => [
                'search_contains' => true,
                'single_backstroke_delete' => false,
            ],
            'options' => [
                'class' => 'form-control',
                'size' => 1,
            ],
            'multiple' => true,
        ])->label(Yii::t('ra/settings', 'Columns')) ?>

    </div>

    <?= $form->field($model, 'settings[hasCategory]', ['options' => [
        'style' => 'display:' . (!empty($model->settings['hasChild']) ? 'none' : 'block')
    ]])->checkbox(['label' => Yii::t('ra/settings', 'Has Category'), 'onchange' => '$(this).parents("div:first").next("div").toggle()']) ?>
    <?= $form->field($model, 'settings[hasChild]', ['options' => [
        'style' => 'display:' . (!empty($model->settings['hasCategory']) ? 'none' : 'block')
    ]])->checkbox(['label' => Yii::t('ra/settings', 'Has Child'), 'onchange' => '$(this).parents("div:first").prev("div").toggle()']) ?>
    <?= $form->field($model, 'settings[hasUrl]')->checkbox(['label' => Yii::t('ra/settings', 'Has Url')]) ?>
    <?= $form->field($model, 'settings[status]')->checkbox(['label' => Yii::t('ra/settings', 'Active On Default')]) ?>
    <?= $form->field($model, 'settings[characters]')->checkbox(['label' => Yii::t('ra/settings', 'Show Characters'), 'onchange' => '$(this).parents("div:first").nextAll(".input-toggle:first").toggle()']) ?>
    <div class="input-toggle col-md-offset-1 form-horizontal"
         style="display: <?= !empty($model->settings['characters']) ? 'block' : 'none' ?>">
        Here we will add characters
    </div>
    <?= $form->field($model, 'settings[data]')->checkbox(['label' => Yii::t('ra/settings', 'Show Data')]) ?>
    <?= $form->field($model, 'settings[seo]')->checkbox(['label' => Yii::t('ra/settings', 'Show Seo')]) ?>
    <?= $form->field($model, 'settings[position]')->checkbox(['label' => Yii::t('ra/settings', 'Show Position')]) ?>
    <?= $form->field($model, 'settings[price]')->checkbox(['label' => Yii::t('ra/settings', 'Show Price')]) ?>
    <?= $form->field($model, 'settings[photos]')->checkbox(['label' => Yii::t('ra/settings', 'Show Photos'), 'onchange' => '$(this).parents("div:first").nextAll(".input-toggle:first").toggle()']) ?>
    <div class="input-toggle col-md-offset-1 form-horizontal"
         style="display: <?= !empty($model->settings['photos']) ? 'block' : 'none' ?>">
        <?= $form->field($model, 'settings[photosCount]')->textInput(['type' => 'number', 'class' => 'col-md-10'])->label(Yii::t('ra/settings', 'Photo Count'), ['class' => 'col-md-2 right']) ?>
        <?= $form->field($model, 'settings[photosTypes]')->textInput(['type' => 'text', 'class' => 'col-md-10'])->label(Yii::t('ra/settings', 'Photo Types'), ['class' => 'col-md-2 right']) ?>
    </div>
    <?= $form->field($model, 'settings[controller]')->checkbox(['label' => Yii::t('ra/settings', 'Is Controller')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
