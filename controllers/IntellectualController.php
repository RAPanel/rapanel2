<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 03.09.2015
 * Time: 22:37
 */

namespace ra\admin\controllers;


use ra\admin\models\Page;
use yii\web\Request;

class IntellectualController extends Controller
{
    public function goBack($defaultUrl = null)
    {
        $defaultUrl = \Yii::$app->request->url;
        return $this->redirect($defaultUrl);
    }

    public function actionDebug()
    {
        $_COOKIE['debug'] = $_SESSION['debug'] = true;
        return $this->goBack();
    }

    public function actionCache()
    {
        $c = new ClearController('clear', $this->module);
        $c->actionCache(false);
        return $this->goBack();
    }

    public function actionEdit()
    {
        $get = \Yii::$app->urlManager->parseRequest(new Request(['url' => \Yii::$app->request->url]));
        if (!empty($get['id'])) $model = Page::findOne($get['id']);
        if (!empty($get['url'])) $model = Page::findOne(['url' => $get['url']]);
        if(isset($model)) {
            $c = new TableController('table', $this->module);
            return $c->actionUpdate($model->id);
        }
        return false;
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goBack();
    }
}