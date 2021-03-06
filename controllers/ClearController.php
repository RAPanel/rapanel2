<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 11.09.2015
 * Time: 8:41
 */

namespace ra\admin\controllers;


use ra\admin\models\PageData;
use ra\admin\models\Photo;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

class ClearController extends AdminController
{
    public function actionIndex($back = true)
    {
        $this->actionCache(false);
        $this->actionAssets(false);
        $this->actionImages(false);
        if ($back && !Yii::$app->request->isAjax)
            return $this->back($back);
        return true;
    }

    public function actionCache($back = true)
    {
        $result = Yii::$app->cache->flush();
        if ($back && !Yii::$app->request->isAjax)
            return $this->back($back);
        return $result;
    }

    public function back($back)
    {
        $parse = explode('_pjax', Yii::$app->request->referrer);
        return $this->redirect(is_string($back) ? $back : trim(reset($parse), '&?'));
    }

    public function actionAssets($back = true)
    {
        $dir = Yii::getAlias(Yii::$app->assetManager->basePath);
        foreach (scandir($dir) as $name)
            if (is_dir($dir . '/' . $name) && !in_array($name, ['.', '..']))
                FileHelper::removeDirectory($dir . '/' . $name);
        if ($back && !Yii::$app->request->isAjax)
            return $this->back($back);
        return true;
    }

    public function actionImages($back = true)
    {
        // Delete image upload cache
        FileHelper::removeDirectory('@runtime/uploadedFiles');

        // Delete not existing files in data base
        $dir = Yii::getAlias('@webroot/' . Photo::$tmpPath . '/');
        foreach (scandir($dir) as $name) if (is_file($dir . $name))
            if (!Photo::find()->where(['name' => $name])->exists())
                unlink($dir . $name);

        // Remove resize dirs
        $dir = Yii::getAlias('@webroot/' . Photo::$path . '/');
        foreach (scandir($dir) as $name)
            if (is_dir($dir . $name) && strpos($name, '_') === 0)
                FileHelper::removeDirectory($dir . '/' . $name);
        if ($back && !Yii::$app->request->isAjax)
            return $this->back($back);
        return true;
    }

    public function actionSeo()
    {
        $data = [
            'title' => PageData::find()->joinWith('page', false)->where(['or', 'title=header', 'title=name', 'title LIKE REPLACE(name, " ", "%")']),
            'description' => PageData::find()->joinWith('page', false)->where(['or', 'description=about', 'content LIKE CONCAT_WS("", "%", REPLACE(REPLACE(description, ".", "%"), " ", "%"), "%")', 'about LIKE CONCAT_WS("", "%", REPLACE(REPLACE(description, ".", "%"), " ", "%"), "%")']),
            'keywords' => PageData::find()->where(['or', 'keywords=tags', 'keywords LIKE REPLACE(tags, ",", "%")']),
        ];
        $result = [];
        foreach ($data as $key => $query) {
            $query->andWhere(['!=', $key, '']);
            $transaction = Yii::$app->db->beginTransaction();
            if (empty($result[$key])) $result[$key] = 0;
            foreach ($query->each() as $model) {
                $model->{$key} = '';
                $model->save(false, [$key]);

                $result[$key]++;
            }
            $transaction->commit();
        }
        return VarDumper::dumpAsString($result, 10, 1);

    }
}