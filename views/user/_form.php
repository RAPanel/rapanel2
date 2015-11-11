<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\admin\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->checkbox(['label'=>'Активен', 'checked' => $model->status == 1 || $model->isNewRecord]) ?>

    <?= $form->field($model, 'role_id')->dropDownList(\yii\helpers\ArrayHelper::map(\app\admin\models\UserRole::find()->orderBy(['id'=>SORT_DESC])->select(['id', 'name'])->asArray()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'newPassword')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ban_time')->textInput() ?>

    <?= $form->field($model, 'ban_reason')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('ra/view', 'Create') : Yii::t('ra/view', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
