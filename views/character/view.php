<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\admin\models\Character */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rere.view', 'Characters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rere.view', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('rere.view', 'Delete'), ['delete', 'id' => $model->id], [
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
            'id',
            'url:url',
            'type',
            'multi',
            'data',
        ],
    ]) ?>

</div>
