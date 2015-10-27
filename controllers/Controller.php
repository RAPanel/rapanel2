<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 22.10.2015
 * Time: 0:06
 */

namespace ra\admin\controllers;


use Yii;

class Controller extends \yii\web\Controller
{
    public function render($view, $params = [])
    {
        $type = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return parent::$type($view, $params);
    }
}