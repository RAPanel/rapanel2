<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.05.2015
 * Time: 16:31
 */

namespace app\admin\controllers;

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
        $class = Module::find()->select('class')->where(['url' => $this->id])->scalar();
        /** @var Page $class */
        $page = $class::find()->where($condition)->one();
        if (!$condition || !$page) throw new HttpException(404, Yii::t('rere.error', 'Can`t find page'));
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
                if ($model->parent_id) foreach ($model->parents()->all() as $row)
                    $result[] = ['label' => $row->name, 'url' => $row->href];
                $result[] = $model->name;
                return $result;
            };
        }
        return parent::render($view, $params);
    }

}