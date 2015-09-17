<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Module */

$this->title = Yii::t('ra/view', 'Settings {modelClass}: ', [
    'modelClass' => 'Module',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra/view', 'Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['table/index', 'url'=>$model->url]];
$this->params['breadcrumbs'][] = Yii::t('ra/view', 'Settings');
?>
<div class="module-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
