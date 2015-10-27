<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model ra\admin\models\Subscribe */

$this->title = Yii::t('ra', 'Create Subscribe');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra', 'Subscribes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscribe-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
