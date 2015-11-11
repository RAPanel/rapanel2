<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 20.10.2015
 * Time: 23:31
 *
 * @var $model LoginForm
 */
use app\admin\models\forms\LoginForm;
use yii\widgets\ActiveForm;

$this->title = Yii::t('ra/view', 'Sign in now');
?>


<div id="login-page">

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-login'],
    ]); ?>

    <h2 class="form-login-heading"> <?= $this->title ?></h2>

    <div class="login-wrap">
        <?= $form->field($model, 'username')->textInput(['placeholder' => $model->getAttributeLabel('username')])->label(false) ?>
        <br>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>

        <label class="checkbox">
                <span class="pull-right">
                    <a data-toggle="modal" href="<?= \yii\helpers\Url::to(['login']) ?>#myModal"> Forgot Password?</a>
                </span>
        </label>
        <button class="btn btn-theme btn-block" href="index.html" type="submit">
            <i class="fa fa-lock"></i> <?= Yii::t('ra/view', 'SIGN IN') ?>
        </button>

    </div>

    <!-- Modal -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal"
         class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Forgot Password ?</h4>
                </div>
                <div class="modal-body">
                    <p>Enter your e-mail address below to reset your password.</p>
                    <input type="text" name="email" placeholder="Email" autocomplete="off"
                           class="form-control placeholder-no-fix">

                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>
                    <button class="btn btn-theme" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- modal -->

    <?php ActiveForm::end(); ?>
</div>
