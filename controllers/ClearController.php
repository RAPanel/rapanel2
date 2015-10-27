<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 11.09.2015
 * Time: 8:41
 */

namespace ra\admin\controllers;


use ra\admin\models\Photo;
use Yii;
use yii\helpers\FileHelper;

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
        $dir = Yii::getAlias('@webroot/' . Photo::$path);
        foreach (scandir($dir) as $name)
            if (is_dir($dir . '/' . $name) && strpos($name, '_') === 0)
                FileHelper::removeDirectory($dir . '/' . $name);
        if ($back && !Yii::$app->request->isAjax)
            return $this->back($back);
        return true;
    }
}