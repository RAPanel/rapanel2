<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ra\admin\models\Page */
/* @var $form yii\widgets\ActiveForm */

$settings = \ra\admin\helpers\RA::moduleSetting($model->module_id);
$tabs = ['main', 'data', 'seo', 'position', 'characters', 'photos'];
?>

<div class="page-form">

    <?= Html::a(Yii::t('ra', 'Cancel'), ['index', 'url' => $model->module->url, 'id' => $model->parent_id], ['class' => 'btn btn-warning pull-right', 'onclick' => 'return history.back()']) ?>

    <ul class="nav nav-tabs" style="margin-bottom: 0">
        <? foreach (\ra\admin\helpers\RA::dropDownList($tabs, 'ra') as $key => $value) if ($key == 'main' || !empty($settings[$key])): ?>
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
                        <? foreach ($tabs as $i => $key): ?>
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
                                            <?= $form->field($model, 'created_at')->textInput(['type' => 'date', 'value' => preg_replace('#\s.*#', '', $model->created_at)]) ?>
                                        </div>
                                    </div>

                                    <?= $form->field($model, 'about')->textarea(['maxlength' => true, 'rows' => 5]) ?>

                                <? elseif ($key == 'position' && !empty($settings[$key])): ?>

                                    <? if (!empty($settings['hasChild'])) echo $form->field($model, 'is_category')->checkbox(['label' => Yii::t('ra', 'Can has child`s')]) ?>

                                    <? $categories = \yii\helpers\ArrayHelper::map($model::findActive($model->module_id, ['and', ['is_category' => 1], ['!=', 'id', $model->id]], true)->select(['id', 'name', 'level'])->all(), 'id', function ($data) {
                                        $add = '';
                                        for ($i = 0; $i < $data->level; $i++) $add .= '*';
                                        return "{$add}#{$data->id} {$data->name}";
                                    }) ?>

                                    <?= $form->field($model, 'parent_id')->dropDownList(\yii\helpers\ArrayHelper::merge(empty($model->parent_id) ? [null => Yii::t('ra', 'Select Parent')] : [], $categories)) ?>

                                    <?= $form->field($model, 'user_id')->dropDownList(empty($model->user_id) ? [null => Yii::t('ra', 'Select User')] : [$model->user->id => $model->user->username]) ?>

                                <? elseif ($key == 'seo' && !empty($settings[$key])): ?>

                                    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

                                    <?= $form->field($model, 'pageData[header]')->textInput(['maxlength' => true])->label(Yii::t('ra', 'Header')) ?>

                                    <?= $form->field($model, 'pageData[title]')->textInput(['maxlength' => true])->label(Yii::t('ra', 'Title')) ?>

                                    <?= $form->field($model, 'pageData[description]')->textarea(['maxlength' => true])->label(Yii::t('ra', 'Description')) ?>

                                    <?= $form->field($model, 'pageData[keywords]')->textarea(['maxlength' => true])->label(Yii::t('ra', 'Keywords')) ?>

                                <? elseif ($key == 'characters' && !empty($settings[$key])): ?>

                                    <?= $form->field($model, 'pageCharacters')->widget(ra\admin\widgets\Characters::className())->label(false) ?>

                                <? elseif ($key == 'photos' && !empty($settings[$key])): ?>

                                    <?= $form->field($model, 'photos')->widget(ra\admin\widgets\PhotoUpload::className(), [
                                        'url' => ['upload', 'id' => $model->id, 'table' => $model->tableName()],
                                    ])->label(false) ?>

                                <? elseif ($key == 'data' && !empty($settings[$key])): ?>

                                    <?= $form->field($model, 'pageData[content]')->widget(ra\admin\widgets\TinyMce::className())->label(Yii::t('ra', 'Content')) ?>

                                    <?= $form->field($model, 'pageData[tags]')->widget(ra\admin\widgets\Tags::className())->label(Yii::t('ra', 'Tags')) ?>

                                <? endif; ?>
                            </div>
                        <? endforeach ?>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('ra', 'Update'), ['class' => 'btn btn-success', 'name' => 'submit', 'value' => 'back']) ?>
                        <?= Html::submitButton(Yii::t('ra', 'Apply'), ['class' => 'btn btn-primary', 'name' => 'submit', 'value' => 'refresh']) ?>
                        <?= Html::submitButton(Yii::t('ra', 'Save&Go'), ['class' => 'btn btn-info pull-right', 'name' => 'submit', 'value' => 'open']) ?>
                    </div>

                    <div class="clearfix"></div>

                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
