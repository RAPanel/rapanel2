<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ra', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('ra', 'Create Order'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'id' => 'orderTable',
        'dataProvider' => $dataProvider,
        /*'rowOptions'=> function($model, $key, $index, $grid){
            return ['data-id'=>$model->id];
        },*/
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'status_id',
            'is_payed:boolean',
            'delivery',
            'pay',
            'name',
            'email',
            'phone',
            'created_at:date',
//             'address',
//             'comment',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
