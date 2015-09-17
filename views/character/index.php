<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ra/view', 'Characters');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-index">

    <div class="row content-panel">

        <div class="col-lg-12">
            <div class="pull-right">
                <?= Html::a(Yii::t('ra/view', 'Create Character'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>

            <h4><i class="fa fa-angle-right"></i> <?= Html::encode($this->title) ?></h4>

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
    </div>
</div>
