<?php

use ra\admin\widgets\ExcelGrid;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

ExcelGrid::widget([
        'dataProvider' => $dataProvider,
        //'extension'=>'xlsx',
        //'filename'=>'excel',
        'properties' =>[
            //'creator'	=>'',
            //'title' 	=> '',
            //'subject' 	=> '',
            //'category'	=> '',
            //'keywords' 	=> '',
            //'manager' 	=> '',
            //'description'=>'BSOURCECODE',
            //'company'	=>'BSOURCE',
        ],
        'columns' => [
        ],
    ]);

?>