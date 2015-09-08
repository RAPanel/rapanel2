<?php

use app\admin\helpers\RA;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Page */

$this->title = Yii::t('rere.view', 'Edit {modelClass}: ', [
        'modelClass' => $model->is_category ? 'категорию' : 'элемент',
    ]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rere.view', 'Modules'), 'url' => ['module/index']];

if ($model->parent->hasMethod('parents')) {
    foreach (array_reverse($model->parent->parents()->all()) as $row)
        $this->params['breadcrumbs'][] = ['label' => $row->name, 'url' => ['index', 'url' => RA::module($row->module_id), 'id' => $row->id]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
