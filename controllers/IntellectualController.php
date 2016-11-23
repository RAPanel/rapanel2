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
        if (\Yii::$app->request->isAjax) return true;
        $defaultUrl = \Yii::$app->request->referrer;
        return $this->redirect($defaultUrl);
    }

    /*public function actionDebug()
    {
        VarDumper::dump($_COOKIE,10,1);die;
        return $this->goBack();
    }*/

    public function actionCache()
    {
        $c = new ClearController('clear', $this->module);
        $c->actionCache(false);
        return $this->goBack();
    }

    public function actionEdit($iframe = false)
    {
        $url = str_replace(\Yii::$app->request->hostInfo, '', \Yii::$app->request->referrer);
        $get = \Yii::$app->urlManager->parseRequest(new Request(['url' => $url]));
        if ($url) $model = Page::findOne(['url' => $url]);
        if (empty($model) && !empty($get[1])) {
            $get = $get[1];
            if (empty($model) && !empty($get['id'])) $model = Page::findOne($get['id']);
            if (empty($model) && !empty($get['url'])) $model = Page::findOne(['url' => $get['url']]);
        }
        if (isset($model)) {
            return $this->redirect(['table/update', 'id' => $model->id, 'iframe' => $iframe]);
        }
        return $this->renderContent('Page can not be edit');
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goBack();
    }
}