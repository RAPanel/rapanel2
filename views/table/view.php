<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ra\admin\models\Page */

$this->title = $model->name;
require_once(__DIR__ . '/_breadcrumbs.php');
?>
<div class="page-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('ra', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('ra', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('ra', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'status',
            'parent_id',
            'lft',
            'rgt',
            'level',
            'module_id',
            'user_id',
            'url:url',
            'name',
            'about',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
