<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ra/view', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('ra/view', 'Create Order'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'id'=>'orderTable',
        'dataProvider' => $dataProvider,
        /*'rowOptions'=> function($model, $key, $index, $grid){
            return ['data-id'=>$model->id];
        },*/
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'status_id',
            'is_paied:boolean',
            'delivery',
             'pay',
             'name',
             'email',
             'phone',
//             'address',
//             'comment',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
