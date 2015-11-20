<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ra', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-index">

    <div class="row content-panel">

        <div class="col-lg-12">
            <div class="pull-right">
                <?= Html::a(Yii::t('ra', 'Create Settings'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>

            <h4><i class="fa fa-angle-right"></i> <?= Html::encode($this->title) ?></h4>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [

                ],
            ]); ?>

        </div>
    </div>
</div>
