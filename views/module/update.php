<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ra\admin\models\Module */

$this->title = Yii::t('ra', 'Module Settings') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra', 'Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['table/index', 'url' => $model->url]];
$this->params['breadcrumbs'][] = Yii::t('ra', 'Settings');
?>
<div class="module-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <? if ($model->settings['hasCategory'] || $model->settings['hasChild']): ?>
        <div><?= Html::a(Yii::t('ra', 'Fix Tree'), ['table/fix-tree', 'id' => $model->id], ['class' => 'btn btn-danger pull-right']) ?></div>
        <div class="clearfix"></div>
    <? endif ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
