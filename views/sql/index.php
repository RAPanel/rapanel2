<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ra', 'SQL Manager');
$this->params['breadcrumbs'][] = $this->title;

$itemsTable = [];
foreach (Yii::$app->db->createCommand('SHOW FULL TABLES')->queryAll() as $row) {
    $itemsTable[] = ['label' => ($table = str_replace(Yii::$app->db->tablePrefix, '', current($row))), 'url' => ['index', 'table' => $table]];
}
$itemsPresent = [['label' => '--- новый ---', 'url' => ['index']]];
foreach ($model::find()->all() as $row) {
    $itemsPresent[] = ['label' => $row->name, 'url' => ['index', 'id' => $row->id]];
}
?>
<div class="settings-index">

    <div class="row content-panel">

        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['method' => 'get']); ?>

            <div class="form-inline pull-right">
                <span>
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Таблицы <b class="caret"></b></a>
                    <?= \yii\bootstrap\Dropdown::widget([
                        'items' => $itemsTable,
                        'options' => ['style' => 'top:35px;max-height:305px;overflow-y:auto;left:inherit']
                    ]) ?>
                </span>
                <span>
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Снипеты <b class="caret"></b></a>
                    <?= \yii\bootstrap\Dropdown::widget([
                        'items' => $itemsPresent,
                        'options' => ['style' => 'top:35px;max-height:305px;overflow-y:auto;left:inherit']
                    ]) ?>
                </span>
                <?= Html::activeTextInput($model, 'name', ['class' => 'form-control']) ?>
            </div>

            <h4><i class="fa fa-angle-right"></i> <?= Html::encode($this->title) ?></h4>

            <div class="form-group">
                <?= Html::activeTextarea($model, 'value', ['class' => 'form-control']); ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('ra', 'Submit'), ['class' => 'btn btn-primary']) ?>
                <?= Html::submitButton(Yii::t('ra', 'Save'), ['class' => 'btn btn-success pull-right', 'value' => 'true', 'name' => 'save']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <? if ($dataProvider) echo GridView::widget([
            'options' => ['style' => 'overflow-x:auto;width: 100%;'],
            'dataProvider' => $dataProvider,
            'columns' => [

            ],
        ]); ?>

    </div>
</div>
</div>
