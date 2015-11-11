<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\admin\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra/view', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('ra/view', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('ra/view', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('ra/view', 'Are you sure you want to delete this item?'),
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
