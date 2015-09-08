<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rere.view', 'Characters');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rere.view', 'Create Character'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'url:url',
            'type',
            'multi',
            'data',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
