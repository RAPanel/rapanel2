<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model ra\admin\models\Replaces */

$this->title = Yii::t('ra', 'Create Replaces');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra', 'Replaces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="replaces-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
