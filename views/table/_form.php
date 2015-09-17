<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Page */
/* @var $form yii\widgets\ActiveForm */

$settings = \app\admin\helpers\RA::moduleSetting($model->module_id);
?>

<div class="page-form">

    <ul class="nav nav-tabs" style="margin-bottom: 0">
        <? foreach (\app\admin\helpers\RA::tabs() as $key => $value) if ($key == 'main' || !empty($settings[$key])): ?>
            <li class="<?= $key == 'main' ? 'active' : '' ?>"><a href="#<?= $key ?>" data-toggle="tab"><?= $value ?></a>
            </li>
        <? endif ?>
    </ul>

    <div class="content-panel">

        <div class="row">
            <?php $form = ActiveForm::begin(); ?>
            <div class="col-lg-12">
                <div class="col-lg-12">

                    <div class="tab-content">
                        <? foreach (\app\admin\helpers\RA::$tabs as $i => $key): ?>
                            <div class="tab-pane <?= $key == 'main' ? 'active' : '' ?>" id="<?= $key ?>">

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

                                <? elseif ($key == 'position' && $settings[$key]): ?>

                                    <? if($settings['hasChild']) echo $form->field($model, 'is_category')->checkbox(['label'=>Yii::t('ra/view', 'Can has child`s')]) ?>

                                    <?= $form->field($model, 'parent_id')->dropDownList(empty($model->parent_id) ? [null => Yii::t('ra/placeholder', 'Select Parent')] : [$model->parent->id => $model->parent->name]) ?>

                                    <?= $form->field($model, 'user_id')->dropDownList(empty($model->user_id) ? [null => Yii::t('ra/placeholder', 'Select User')] : [$model->user->id => $model->user->username]) ?>

                                <? elseif ($key == 'seo' && $settings[$key]): ?>

                                    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

                                    <?= $form->field($model, 'pageData[title]')->textInput(['maxlength' => true])->label(Yii::t('ra/model', 'Title')) ?>

                                    <?= $form->field($model, 'pageData[description]')->textarea(['maxlength' => true])->label(Yii::t('ra/model', 'Description')) ?>

                                    <?= $form->field($model, 'pageData[keywords]')->textarea(['maxlength' => true])->label(Yii::t('ra/model', 'Keywords')) ?>

                                <? elseif ($key == 'characters' && $settings[$key]): ?>

                                    <?= $form->field($model, 'pageCharacters')->widget(app\admin\widgets\Characters::className())->label(false) ?>

                                <? elseif ($key == 'photos' && $settings[$key]): ?>

                                    <?= $form->field($model, 'photos')->widget(app\admin\widgets\PhotoUpload::className(), [
                                        'url' => ['upload', 'id' => $model->id, 'table' => $model->tableName()],
                                    ])->label(false) ?>

                                <? elseif ($key == 'data' && $settings[$key]): ?>

                                    <?= $form->field($model, 'pageData[content]')->widget(app\admin\widgets\TinyMce::className())->label(Yii::t('ra/model', 'Content')) ?>

                                    <?= $form->field($model, 'pageData[tags]')->widget(app\admin\widgets\Tags::className())->label(Yii::t('ra/model', 'Tags')) ?>

                                <? endif; ?>
                            </div>
                        <? endforeach ?>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('ra/view', 'Create') : Yii::t('ra/view', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>

                    <div class="clearfix"></div>

                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
