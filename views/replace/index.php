<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rere.view', 'Replaces');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="replaces-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rere.view', 'Create Replaces'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'value:ntext',
            'update_at',
            'create_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
