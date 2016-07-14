<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.05.2015
 * Time: 16:31
 */

namespace ra\admin\controllers;

use creocoder\nestedsets\NestedSetsBehavior;
use ra\admin\helpers\RA;
use ra\admin\helpers\Text;
use ra\admin\models\Page;
use Yii;
use yii\helpers\Url;
use yii\web\HttpException;

class PageController extends Controller
{
    public $social = true;

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

    public function render($view, $params = [])
    {
        /** @var $base Page */
        if (isset($params['model'])) {
            $model = $params['model'];
            if (method_exists($model, 'getData') && ($data = $model->data)) {
                $this->registerMetaTitle($model);
                if (!Yii::$app->request->isAjax) {
                    // Registry meta seo data
                    $this->registerMetaDescription($model);
                    $this->registerMetaKeywords($model);
                    // Registry og data
                    if ($this->social) {
                        $this->registerOgMeta($model);
                        $this->registerOgMetaPhoto($model);
                    }
                }
            }
            $this->getView()->params['model'] = $model;
            $this->getView()->params['active'] = [$model->id, $model->parent_id, $model->module_id];
            $this->getView()->params['breadcrumbs'] = function () use ($model) {
                $result = [];
                if ($model->parent_id) {
                    if (!$model->rgt) {
                        $parent = $model->parent;
                    }
                    /** @var Page|NestedSetsBehavior $get */
                    $get = isset($parent) ? $parent : $model;
                    try {
                        $query = $get->parents();
                    } catch (\Exception $e) {
                        $query = $get::findActive($get->module_id, ['and', ['<', 'lft', $get->lft], ['>', 'rgt', $get->rgt]], true, true);
                    }
                    if (!RA::moduleSetting($model->module_id, 'controller'))
                        $query->andWhere(['>', 'parent_id', 0]);
                    $rows = $query->all();
                    if (isset($parent) && (!$model->status || $parent->status)) $rows[] = $parent;
                    foreach ($rows as $row) if (!$model->status || $row->status) {
                        $this->getView()->params['active'][] = $row->id;
                        $result[] = ['label' => $row->name, 'url' => $row->href];
                    }
                    foreach ($result as $key => $row) if (Url::to($row['url']) == '/') unset($result[$key]);
                }
                if ($model->href == Yii::$app->request->url)
                    $result[] = $model->name;
                else
                    $result[] = ['label' => $model->name, 'url' => $model->href];
                return $result;
            };
            // @todo Для удаление в следующей версии
            {
                $params['base'] = $model;
                $this->getView()->params['pageTitle'] = $model->name;
            }
        }
        return parent::render($view, $params);
    }

    /**
     * @param $model Page
     */
    public function registerMetaTitle($model)
    {
        $data = $model->data;
        if ($data && !empty($data['title']))
            $this->getView()->title = $data['title'];

    }

    /**
     * @param $model Page
     */
    public function registerMetaDescription($model)
    {
        $data = $model->data;
        if (!empty($data['description'])) $this->getView()->registerMetaTag(['name' => 'description', 'content' => $data['description']]);
        elseif (!empty($model['about'])) $this->getView()->registerMetaTag(['name' => 'description', 'content' => $model['about']]);
        elseif (!empty($data['content'])) $this->getView()->registerMetaTag(['name' => 'description', 'content' => Text::cleverStrip($data['content'], 200)]);

    }

    public function registerMetaKeywords($model)
    {
        $data = $model->data;
        if (!empty($data['keywords'])) $this->getView()->registerMetaTag(['name' => 'keywords', 'content' => $data['keywords']]);
        elseif (!empty($data['tags'])) $this->getView()->registerMetaTag(['name' => 'keywords', 'content' => $data['tags']]);
    }

    /**
     * @param $model Page
     */
    public function registerOgMeta($model)
    {
        $this->getView()->registerMetaTag(['property' => 'og:type', 'content' => $model->is_category ? 'website' : 'article']);
        if (!empty($model['header'])) $this->getView()->registerMetaTag(['property' => 'og:title', 'content' => $model['header']]);
        if (!empty($model['about'])) $this->getView()->registerMetaTag(['property' => 'og:description', 'content' => $model['about']]);
        if (method_exists($model, 'getHref')) $this->getView()->registerMetaTag(['property' => 'og:url', 'content' => $model->getHref(1, 1)]);
    }

    /**
     * @param $model Page
     */
    public function registerOgMetaPhoto($model)
    {
        if (method_exists($model, 'getPhotos') && $model->photos) {
            foreach ($model->photos as $row) {
                if ($row->type == 'social') $photo = $row;
            }
            if (empty($photo) && count($model->photos)) $photo = $model->photos[0];
        }
        if (empty($photo) && method_exists($model, 'getPhoto') && $model->photo) $photo = $model->photo;
        if (isset($photo)) {
            $this->getView()->registerMetaTag(['property' => 'og:image', 'content' => $photo->getHref('1000', true)]);
            $this->getView()->registerMetaTag(['property' => 'og:image:width', 'content' => $photo->getSizes('1000')['width']]);
            $this->getView()->registerMetaTag(['property' => 'og:image:height', 'content' => $photo->getSizes('1000')['height']]);
        }
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