<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ra\admin\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('ra', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('ra', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('ra', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'role_id',
            'status',
            'email:email',
            'new_email:email',
            'username',
            'password',
            'auth_key',
            'api_key',
            'login_ip',
            'login_time',
            'create_ip',
            'created_at',
            'updated_at',
            'ban_time',
            'ban_reason',
        ],
    ]) ?>

</div>
