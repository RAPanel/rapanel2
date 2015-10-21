<?php

namespace app\admin\controllers;

use app\admin\helpers\RA;
use app\admin\helpers\Text;
use app\admin\models\Module;
use app\admin\models\Photo;
use Yii;
use app\admin\models\Page;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * TableController implements the CRUD actions for Page model.
 */
class TableController extends AdminController
{

    /**
     * @param $url
     * @return \app\admin\models\Module
     * @throws HttpException
     */
    public function getModule($url)
    {
        $module = Module::findOne(compact('url'));
        if (is_null($module) || !($table = $module->class))
            throw new HttpException(404);
        return $module;
    }

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionIndex($url = null, $id = null)
    {
        $module = $this->getModule($url);

        /** @var \yii\db\ActiveRecord $model */
        $model = new $module->class;
        if ($id) $model = $model::findOne($id);

        $query = $model::find()->from(['t' => $model::tableName()])->orderBy(['t.lft' => SORT_ASC, 't.id' => SORT_ASC]);

        if ($model->hasAttribute('module_id')) {
            $query->andWhere(['t.module_id' => $module->id]);
            if (!$id && (!empty($module->settings['hasCategory']) || !empty($module->settings['hasChild'])) && $model->hasAttribute('lft'))
                return Yii::$app->request->get('action') ?
                    $this->redirect([Yii::$app->request->get('action'), 'url' => $module->url,
                        'parent_id' => $id,
                        'is_category' => !empty($module->settings['hasChild'])]) :
                    $this->redirect(['index', 'url' => $url, 'id' => $module->rootId]);
        }
        if ($model->hasAttribute('status')) $query->andWhere(['!=', 't.status', 9]);
        if ($id) $query->andWhere(['t.parent_id' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'model' => $model,
            'module' => $module,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionSearch($url = null, $q = null)
    {
        $module = $this->getModule($url);

        /** @var \yii\db\ActiveRecord $model */
        $model = new $module->class;

        $query = $model::find()->from(['t' => $model::tableName()]);

        if ($model->hasAttribute('status')) $query->andWhere(['!=', 't.status', 9]);
        $query->andWhere(['or', ['id' => $q], ['like', 'name', Text::search($q)]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'model' => $model,
            'module' => $module,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpload($id, $table)
    {
//        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($file = UploadedFile::getInstanceByName('file')) {
            $temp = uniqid() . '.' . pathinfo($file->name, PATHINFO_EXTENSION);
            $dir = Yii::getAlias('@runtime/uploadedFiles/');
            if (Yii::$app->session->id) $dir .= Yii::$app->session->id . '/';
            FileHelper::createDirectory($dir);
            $result = $file->saveAs($dir . $temp);
            if ($result) {
                $model = Photo::add($dir . $temp, pathinfo($file->name, PATHINFO_BASENAME), $id, $table);
                return $this->renderAjax('_image', ['data' => $model, 'index' => Photo::find()->where(['owner_id' => $id, 'model' => $table])->count()]);
            } else return new HttpException(400, 'Не могу сохранить');
        }

        return new HttpException(404);
    }

    /**
     * Displays a single Page model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->redirect($this->findModel($id)->getHref());
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($url, $parent_id = null, $is_category = null)
    {
        $module = $this->getModule($url);

        /** @var \yii\db\ActiveRecord $model */
        $model = new $module->class;

        $model->setAttributes([
            'status' => 9,
            'module_id' => $module->id,
            'parent_id' => $parent_id,
            'is_category' => $is_category
        ], false);

        if ($model->save(false))
            return $this->redirect(['update', 'id' => $model->id]);
        throw new HttpException(402, Yii::t('ra/error', 'Can`t create Post'));
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $_GET['url'] = RA::module($model->module_id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->status = !empty($model->module->settings['status']);
            $model->save();
            return $this->redirect(['index', 'url' => $model->module->url, 'id' => $model->parent_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id, $type = null)
    {
        if ($type == 'photo') Photo::findOne($id)->delete();
        else $this->findModel($id)->delete();

        if (Yii::$app->request->isAjax) return '1';
        return $this->redirect(['index']);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSave($id)
    {
        return Yii::$app->db->createCommand()->update(Page::tableName(), Yii::$app->request->get(), compact('id'))->execute();
    }
}
