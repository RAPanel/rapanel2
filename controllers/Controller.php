<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 03.09.2015
 * Time: 22:37
 */

namespace app\admin\controllers;


use Yii;

class Controller extends \yii\web\Controller
{
    public function render($view, $params = []){
        $type = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return parent::$type($view, $params);
    }

}