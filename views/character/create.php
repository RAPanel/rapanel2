<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model ra\admin\models\Character */

$this->title = Yii::t('ra', 'Create Character');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ra', 'Characters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="character-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
