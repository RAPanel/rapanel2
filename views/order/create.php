<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\admin\models\Order */

$this->title = Yii::t('ra/view', 'Create Order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra/view', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
