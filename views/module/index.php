<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rere.view', 'Modules');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('$("[data-toggle=\"tooltip\"]").tooltip()');
?>
<div class="module-index">

    <div class="row content-panel">

        <div class="col-lg-12">
            <div class="pull-right">
                <?= Html::a(Yii::t('app', 'Create Module'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>

            <h4><i class="fa fa-angle-right"></i> <?= Html::encode($this->title) ?></h4>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['style' => 'width:80px'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => ['style' => 'width:50px'],
                        'template' => '{add} {update}',
                        'buttonOptions' => [
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ],
                        'buttons' => [
                            'add' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-plus"></span>',
                                    ['table/index', 'url' => $model->url, 'action' => 'create'], [
                                        'title' => 'Добавить запись в ' . $model->name,
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                    ]);
                            },
                        ],
                    ],
                    [
                        'attribute' => 'name',
                        'format' => 'html',
                        'value' => function ($data) {
                            return Html::a($data->name, ['table/index', 'url' => $data->url]);
                        }
                    ],
                    'url',
                    [
                        'attribute' => 'created_at',
                        'format' => 'date',
                        'contentOptions' => ['style' => 'width:90px'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => ['style' => 'width:25px'],
                        'template' => '{delete}',
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
