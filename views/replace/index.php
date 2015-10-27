<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ra', 'Replaces');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="replaces-index">

    <div class="row content-panel">

        <div class="col-lg-12">
            <div class="pull-right">
                <?= Html::a(Yii::t('ra', 'Create Replaces'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>

            <h4><i class="fa fa-angle-right"></i> <?= Html::encode($this->title) ?></h4>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'name',
                    'value:ntext',
//                    'update_at',
                    'create_at',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>

        </div>
    </div>
</div>
