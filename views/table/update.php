<?php

/* @var $this yii\web\View */
/* @var $model ra\admin\models\Page */

$this->title = Yii::t('ra', $model->name ? 'Edit {modelClass}: ' : 'Add {modelClass}', [
        'modelClass' => $model->is_category ? 'категорию' : 'элемент',
    ]) . ' ' . $model->name;
require_once(__DIR__ . '/_breadcrumbs.php');

?>
<div class="page-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
