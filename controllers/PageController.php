<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.05.2015
 * Time: 16:31
 */

namespace ra\admin\controllers;

use ra\admin\models\Page;
use ra\admin\traits\SeoRender;
use Yii;
use yii\web\HttpException;

class PageController extends Controller
{
    use SeoRender;

    public function actionIndex($url = null)
    {
        if (is_null($url)) $url = "/$this->id";
        return $this->actionShow($url);
    }

    public function actionShow($url)
    {
        return $this->render($this->action->id, [
            'model' => $this->getPage(compact('url'))
        ]);
    }

    /**
     * @param $condition
     * @return Page
     * @throws HttpException
     */
    public function getPage($condition)
    {
        /** @var Page $class */
        $page = Page::find()->where($condition)->with(['pageData', 'parent', 'photo'])->one();
        if (!$condition || !$page) throw new HttpException(404, Yii::t('ra', 'Can`t find page'));
        return $page;
    }

    public function actionCategory($url = null)
    {
        return $this->actionShow($url);
    }

}