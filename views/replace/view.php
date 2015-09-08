<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Replaces */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rere.view', 'Replaces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="replaces-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rere.view', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('rere.view', 'Delete'), ['delete', 'id' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('rere.view', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'value:ntext',
            'update_at',
            'create_at',
        ],
    ]) ?>

</div>
