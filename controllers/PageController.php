<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.05.2015
 * Time: 16:31
 */

namespace app\admin\controllers;

use app\admin\helpers\RA;
use app\admin\models\Module;
use app\admin\models\Page;
use Yii;
use yii\web\HttpException;

class PageController extends Controller
{
    public function actionIndex($url = null)
    {
        if (is_null($url)) $url = "/$this->id";
        return $this->actionShow($url);
    }

    public function actionCategory($url = null)
    {
        return $this->actionShow($url);
    }

    public function actionShow($url)
    {
        return $this->render($this->action->id, [
            'model' => $this->page(compact('url'))
        ]);
    }

    public function page($condition)
    {
        /** @var Page $class */
        $page = Page::find()->where($condition)->with('pageData')->one();
        if (!$condition || !$page) throw new HttpException(404, Yii::t('ra/error', 'Can`t find page'));
        return $page;
    }

    public function render($view, $params = [])
    {
        /** @var $base Page */
        if (isset($params['model'])) {
            if (method_exists($params['model'], 'getData') && ($data = $params['model']->data)) {
                if (!empty($data['title'])) $this->getView()->title = $data['title'];
                if (!empty($data['description'])) $this->getView()->registerMetaTag(['name' => 'description', 'content' => $data['description']]);
                if (!empty($data['keywords'])) $this->getView()->registerMetaTag(['name' => 'keywords', 'content' => $data['keywords']]);
            }
            $this->getView()->params['model'] = $params['base'] = $params['model'];
            $this->getView()->params['breadcrumbs'] = function () {
                $model = $this->getView()->params['model'];
                $result = [];
                if ($model->parent_id) {
                    if (!$model->level) {
                        $parent = $model::findOne($model->parent_id);
                    }
                    $get = isset($parent) ? $parent : $model;
                    $query = $get::findActive($get->module_id,['between', 'lft', $get->lft, $get->rgt])->andWhere(['!=', 'id', $get->id]);
                    if(!RA::moduleSetting($model->module_id, 'controller'))
                        $query->andWhere(['<>', 'parent_id', null]);
                    $rows = $query->all();
                    if(isset($parent)) $rows[] = $parent;
                    foreach ($rows as $row)
                        $result[] = ['label' => $row->name, 'url' => $row->href];
                }
                $result[] = $model->name;
                return $result;
            };
        }
        return parent::render($view, $params);
    }

}