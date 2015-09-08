<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\admin\models\Module */

$this->title = Yii::t('app', 'Create Module');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
