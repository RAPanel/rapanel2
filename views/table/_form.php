<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Page */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="page-form">

    <ul class="nav nav-tabs" style="margin-bottom: 20px">
        <? foreach (\app\admin\helpers\RA::tabs() as $key => $value): ?>
            <li class="<?= $key == 'main' ?'active':'' ?>"><a href="#<?= $key ?>" data-toggle="tab"><?= $value ?></a></li>
        <? endforeach ?>
    </ul>

    <?php $form = ActiveForm::begin(); ?>

    <div class="tab-content">
        <? foreach (\app\admin\helpers\RA::$tabs as $i => $key): ?>
            <div class="tab-pane <?= $key == 'main' ?'active':'' ?>" id="<?= $key ?>">

                <? if ($key == 'main'): ?>

                    <div class="row">
                        <div class="col-md-9">
                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'status')->checkbox() ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'created_at')->textInput(['type' => 'date']) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'about')->textarea(['maxlength' => true, 'rows' => 5]) ?>

                <? elseif ($key == 'position'): ?>

                    <?= $form->field($model, 'parent_id')->dropDownList(empty($model->parent_id) ? [null => Yii::t('rere.placeholder', 'Select Parent')] : [$model->parent->id => $model->parent->name]) ?>

                    <?= $form->field($model, 'user_id')->dropDownList(empty($model->user_id) ? [null => Yii::t('rere.placeholder', 'Select User')] : [$model->user->id => $model->user->name]) ?>

                <? elseif ($key == 'seo'): ?>

                    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'pageData[title]')->textInput(['maxlength' => true])->label(Yii::t('rere.model', 'Title')) ?>

                    <?= $form->field($model, 'pageData[description]')->textarea(['maxlength' => true])->label(Yii::t('rere.model', 'Description')) ?>

                    <?= $form->field($model, 'pageData[keywords]')->textarea(['maxlength' => true])->label(Yii::t('rere.model', 'Keywords')) ?>

                <? elseif ($key == 'characters'): ?>

                    <?= $form->field($model, 'pageCharacters')->widget(app\admin\widgets\Characters::className())->label(false) ?>

                <? elseif ($key == 'photos'): ?>

                    <?= $form->field($model, 'photos')->widget(app\admin\widgets\PhotoUpload::className(), [
                        'url' => ['upload', 'id' => $model->id, 'table' => $model->tableName()],
                    ])->label(false) ?>

                <? elseif ($key == 'data'): ?>

                    <?= $form->field($model, 'pageData[content]')->widget(app\admin\widgets\TinyMce::className())->label(Yii::t('rere.model', 'Content')) ?>

                    <?= $form->field($model, 'pageData[tags]')->widget(app\admin\widgets\Tags::className())->label(Yii::t('rere.model', 'Tags')) ?>

                <? endif; ?>
            </div>
        <? endforeach ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rere.view', 'Create') : Yii::t('rere.view', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
