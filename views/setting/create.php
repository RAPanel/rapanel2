<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model ra\admin\models\Settings */

$this->title = Yii::t('ra', 'Create Settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
