<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rere.view', 'Messages');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="message-index">

    <div class="row content-panel">

        <div class="col-lg-12">
            <div class="pull-right">
                <?= Html::a(Yii::t('rere.view', 'Create Message'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>

            <h4><i class="fa fa-angle-right"></i> <?= Html::encode($this->title) ?></h4>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'category',
                    'message:ntext',
                    [
                        'attribute' => 'messageTranslate.translation',
                        'contentOptions' => ['style' => 'width:50%', 'class' => 'editable'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::tag('div', $model->messageTranslate->translation, ['data-id' => $model->id, 'data-language' => $model->messageTranslate->language, 'data-translation' => $model->messageTranslate->translation]);
                        },
                    ]
                ],
            ]); ?>

        </div>
    </div>
</div>
