<?php
namespace ra\admin\traits;

use creocoder\nestedsets\NestedSetsBehavior;
use ra\admin\helpers\RA;
use ra\admin\helpers\Text;
use ra\admin\models\Page;
use Yii;
use yii\base\Event;
use yii\helpers\Url;
use yii\web\View;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 30.08.2016
 * Time: 17:01
 */
trait SeoRender
{
    public $social = true;
    public $meta = true;

    public $forceMeta = false;

    public function render($view, $params = [], $context = null)
    {
        if (isset($params['model']) && $params['model'] instanceof \ra\admin\models\Page) {
            /** @var $model Page */
            $model = $params['model'];
            if (method_exists($model, 'getData') && ($data = $model->data) && !Yii::$app->request->isAjax) {

                $renderMeta = $this->meta;
                $this->view->on(View::EVENT_AFTER_RENDER, function ($event) use ($params, &$renderMeta) {
                    if ($renderMeta && json_encode($event->params ) == json_encode($params)) {
                        // Registry meta seo data
                        $this->registerMetaTitle($event->data);
                        $this->registerMetaDescription($event->data);
                        $this->registerMetaKeywords($event->data);
                        // Registry og data
                        if ($this->social) {
                            $this->registerOgMeta($event->data);
                            $this->registerOgMetaPhoto($event->data);
                        }
                        $renderMeta = false;
                    }
                }, $model);
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
        }
        return parent::render($view, $params);
    }

    /**
     * @param $event Event
     */
    public function registerMetaTitle($model)
    {
        $view = $this->view;

        if (!$view->title || $this->forceMeta)
            if (!empty($model->data['title'])) $view->title = $model->data['title'];
            elseif (!empty($model->data['h1'])) $view->title = $model->data['h1'];
            elseif (!empty($model['name'])) $view->title = $model['name'];
    }

    static function metaExist($name, $tags = [])
    {
        if (!empty($tags)) foreach ($tags as $key => $tag)
            if (preg_match('#name=[\"\']?' . $name . '[\"\']?#', $tag) && preg_match('#content=[\"\']?([^\'\"]+)[\"\']?#', $tag, $matches)) {
                return [$key, $matches[1]];
            }
        return null;
    }

    static function metaRemove($name)
    {
        foreach ((array)Yii::$app->view->metaTags as $key => $tag)
            if (preg_match('#name=[\"\']?' . $name . '[\"\']?#', $tag))
                unset(Yii::$app->view->metaTags[$key]);
    }

    /**
     * @param $event Event
     */
    public function registerMetaDescription($model)
    {
        /** @var View $view */
        $view = $this->view;

        $id = 'description';
        $description = !empty($model->data['description']) ? $model->data['description'] : (isset($view->params['defaultDescription']) ? $view->params['defaultDescription'] : '');

        if (isset($view->params['description'])) $description = $view->params['description'];
        elseif (!$description) {
            if ($content = self::metaExist('description', $view->metaTags)) list($id, $description) = $content;
            elseif (!empty($model['about'])) $description = $model['about'];
            elseif (!empty($model->data['content'])) $description = Text::cleverStrip($model->data['content'], 200);

            $description = trim(preg_replace('#[\r\n\s]+#', ' ', $description));
            if (mb_strlen($description) > 255)
                $this->description = mb_substr($description, 0, mb_strrpos(mb_substr($description, 0, 255, 'UTF-8'), ' ', 0, 'UTF-8'), 'UTF-8');
        }

        if ($description) {
            self::metaRemove('description');
            $view->registerMetaTag(['name' => 'description', 'content' => $description], $id);
        }

    }

    /**
     * @param $event Event
     */
    public function registerMetaKeywords($model)
    {
        /** @var View $view */
        $view = $this->view;

        $id = 'keywords';
        $keywords = !empty($model->data['keywords']) ? $model->data['keywords'] : (isset($view->params['defaultKeywords']) ? $view->params['defaultKeywords'] : '');

        if (isset($view->params['keywords'])) $keywords = $view->params['keywords'];
        elseif (!$keywords) {
            if ($content = self::metaExist('keywords', $view->metaTags)) list($id, $keywords) = $content;
            elseif (!empty($model->data['tags'])) $keywords = $model->data['tags'];
        }
        if ($content = self::metaExist('keywords', $view->metaTags)) list($id) = $content;

        if ($keywords) {
            self::metaRemove('keywords');
            $view->registerMetaTag(['name' => 'keywords', 'content' => $keywords], $id);
        }
    }

    /**
     * @param $event Event
     */
    public function registerOgMeta($model)
    {
        /** @var View $view */
        $view = $this->view;

        $view->registerMetaTag(['property' => 'og:type', 'content' => $model->is_category ? 'website' : 'article']);
        if (!empty($model['header'])) $view->registerMetaTag(['property' => 'og:title', 'content' => $model['header']]);
        if (!empty($model['about'])) $view->registerMetaTag(['property' => 'og:description', 'content' => $model['about']]);
        if (method_exists($model, 'getHref')) $view->registerMetaTag(['property' => 'og:url', 'content' => $model->getHref(1, 1)]);
    }

    /**
     * @param $event Event
     */
    public function registerOgMetaPhoto($model)
    {
        /** @var View $view */
        $view = $this->view;

        if (method_exists($model, 'getPhotos') && $model->photos) {
            foreach ($model->photos as $row) {
                if ($row->type == 'social') $photo = $row;
            }
            if (empty($photo) && isset($model->photos[0])) $photo = $model->photos[0];
        }
        if (empty($photo) && method_exists($model, 'getPhoto') && $model->photo) $photo = $model->photo;
        if (isset($photo)) {
            $view->registerMetaTag(['property' => 'og:image', 'content' => $photo->getHref('1000', true)]);
            $view->registerMetaTag(['property' => 'og:image:width', 'content' => $photo->getSizes('1000')['width']]);
            $view->registerMetaTag(['property' => 'og:image:height', 'content' => $photo->getSizes('1000')['height']]);
        }
    }
}