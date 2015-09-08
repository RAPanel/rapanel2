<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.05.2015
 * Time: 16:31
 */

namespace app\admin\controllers;

use app\admin\models\Page;
use Yii;
use yii\web\HttpException;

class PageController extends Controller
{
    public $model = '\rere\core\models\Page';

    public function actionIndex($url = null)
    {
        if (is_null($url)) $url = "/$this->id";
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
        /** @var Page $model */
        $model = new $this->model;
        $page = $model::find()->where($condition)->one();
        if (!$condition || !$page) throw new HttpException(404, Yii::t('rere.error', 'Can`t find page'));
        return $page;
    }

    public function render($view, $params = [])
    {
        // @todo Для старых проектов
        if (empty($params['model']) && !empty($params['base'])) $params['model'] = $params['base'];
        /** @var $base Page */
        if (isset($params['model'])) {
            if (method_exists($params['model'], 'getData') && ($data = $params['model']->data)) {
                if (!empty($data['title'])) $this->getView()->title = $data['title'];
                if (!empty($data['description'])) $this->getView()->registerMetaTag(['name' => 'description', 'content' => $data['description']]);
                if (!empty($data['keywords'])) $this->getView()->registerMetaTag(['name' => 'keywords', 'content' => $data['keywords']]);
            }
            $this->getView()->params['model'] = $params['base'] = $params['model'];
        }
        return parent::render($view, $params);
    }

}