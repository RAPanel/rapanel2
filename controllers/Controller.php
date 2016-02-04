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
    public $includePjaxLayout = true;

    public function render($view, $params = [])
    {
        $type = (Yii::$app->request->isAjax || Yii::$app->request->get('ajax')) && (!$this->includePjaxLayout || !Yii::$app->request->isPjax) ? 'renderAjax' : 'render';
        return parent::$type($view, $params);
    }
}