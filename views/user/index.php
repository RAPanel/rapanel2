<?php

use yii\helpers\Html;
use yii\grid\GridView;

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
            'username',
            'email:email',
//            'new_email:email',
            // 'password',
            // 'auth_key',
            // 'api_key',
             'login_ip',
             'login_time:date',
            [
                'attribute'=>'role_id',
                'value'=>function($data){
                    return $data->role->name;
                }
            ],
            // 'create_ip',
             'created_at:date',
            // 'updated_at',
            // 'ban_time',
            // 'ban_reason',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
