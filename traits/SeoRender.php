<?php
namespace ra\admin\traits;

use ra\admin\helpers\RA;
use ra\admin\helpers\Text;
use ra\admin\models\Page;
use Yii;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 30.08.2016
 * Time: 17:01
 */
trait SeoRender
{
    public $social = true;

    public $forceMeta = false;

    public function render($view, $params = [])
    {
        /** @var $base Page */
        if (isset($params['model']) && $params['model'] instanceof \ra\admin\models\Page) {
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
        if (!$this->getView()->title || $this->forceMeta)
            if (!empty($model->data['title'])) $this->getView()->title = $model->data['title'];
            elseif (!empty($model->data['h1'])) $this->getView()->title = $model->data['h1'];
            elseif (!empty($model['name'])) $this->getView()->title = $model['name'];
    }

    /**
     * @param $model Page
     */
    public function registerMetaDescription($model)
    {
        if (!empty($model->data['description'])) $this->getView()->registerMetaTag(['name' => 'description', 'content' => $model->data['description']], 'mainDescription');
        elseif (!empty($model['about'])) $this->getView()->registerMetaTag(['name' => 'description', 'content' => $model['about']], 'mainDescription');
        elseif (!empty($model->data['content'])) $this->getView()->registerMetaTag(['name' => 'description', 'content' => Text::cleverStrip($model->data['content'], 200)], 'mainDescription');

    }

    public function registerMetaKeywords($model)
    {
        if (!empty($model->data['keywords'])) $this->getView()->registerMetaTag(['name' => 'keywords', 'content' => $model->data['keywords']], 'mainKeywords');
        elseif (!empty($model->data['tags'])) $this->getView()->registerMetaTag(['name' => 'keywords', 'content' => $model->data['tags']], 'mainKeywords');
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
            if (empty($photo) && isset($model->photos[0])) $photo = $model->photos[0];
        }
        if (empty($photo) && method_exists($model, 'getPhoto') && $model->photo) $photo = $model->photo;
        if (isset($photo)) {
            $this->getView()->registerMetaTag(['property' => 'og:image', 'content' => $photo->getHref('1000', true)]);
            $this->getView()->registerMetaTag(['property' => 'og:image:width', 'content' => $photo->getSizes('1000')['width']]);
            $this->getView()->registerMetaTag(['property' => 'og:image:height', 'content' => $photo->getSizes('1000')['height']]);
        }
    }
}