<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Replaces */

$this->title = Yii::t('ra/view', 'Update {modelClass}: ', [
    'modelClass' => 'Replaces',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra/view', 'Replaces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('ra/view', 'Update');
?>
<div class="replaces-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
