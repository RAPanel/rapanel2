<?php

namespace app\admin\controllers;

use app\admin\models\Settings;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * SettingController implements the CRUD actions for Settings model.
 */
class SettingController extends AdminController
{
    /**
     * Lists all Settings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Settings::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Settings model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Settings::findOne($id),
        ]);
    }

    /**
     * Creates a new Settings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Settings();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionSave()
    {
        extract(Yii::$app->request->post());
        return Yii::$app->db->createCommand()->update(Settings::tableName(), compact('value'), compact('id'))->execute();
    }
}
