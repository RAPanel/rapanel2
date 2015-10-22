<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ra/view', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('ra/view', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:90px'],
                'template' => '{status} {view} {update} {add}',
                'buttonOptions' => [
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                ],
                'buttons' => [
                    'status' => function ($url, $model, $key) {
                        return Html::a('<i class="fa fa-toggle-' . ($model->status ? 'on' : 'off') . '"></i>', ['save', 'id' => $model->id, 'status' => !$model->status], [
                            'data-toggle' => 'tooltip',
                            'title' => ($model->status ? 'Скрыть' : 'Отобразить'),
                            'class' => 'changeStatus pull-right',
                        ]);
                    }/*,
                            'view' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-eye></i>', ['save', 'id' => $model->id, 'status' => !$model->status], [
                                    'data-toggle' => 'tooltip',
                                    'title' => ($model->status ? 'Скрыть' : 'Отобразить'),
                                    'class' => 'changeStatus pull-right',
                                ]);
                            }*/
                ],
            ],
            'username',
            'email:email',
//            'new_email:email',
            // 'password',
            // 'auth_key',
            // 'api_key',
            'login_ip',
            'login_time:date',
            [
                'attribute' => 'role_id',
                'value' => function ($data) {
                    return $data->role->name;
                }
            ],
            // 'create_ip',
            // 'updated_at',
            // 'ban_time',
            // 'ban_reason',
            [
                'attribute' => 'created_at',
                'label' => 'Создано',
                'format' => 'date',
                'contentOptions' => ['style' => 'width:81px'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:25px'],
                'template' => '{delete}',
            ],
        ],
    ]); ?>

</div>
