<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Settings */

$this->title = Yii::t('ra/view', 'Update {modelClass}: ', [
    'modelClass' => 'Settings',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra/view', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('ra/view', 'Update');
?>
<div class="settings-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
