<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ra\admin\models\Character */

$this->title = Yii::t('ra', 'Update {modelClass}: ', [
        'modelClass' => 'Character',
    ]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra', 'Characters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('ra', 'Update');
?>
<div class="character-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
