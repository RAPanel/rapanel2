<?php

namespace ra\admin\controllers;

use ra\admin\models\Sql;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Exception;
use yii\db\Query;

class SqlController extends AdminController
{
    /**
     * Lists all Form models.
     * @return mixed
     */
    public function actionIndex($id = null, $table = null, $sql = null)
    {
        if ($id) $model = Sql::findOne($id);
        else $model = new Sql();

        if ($sql) $model->value = $sql;

        if ($model->value) {
            list($select, $request) = preg_split('#\sfrom\s#iu', $model->value);
            try {
                $total = Yii::$app->db->createCommand('SELECT COUNT(*) FROM ' . $request)->queryScalar();
            } catch (Exception $e) {
                $total = null;
            }
            $dataProvider = new SqlDataProvider([
                'sql' => $model->value,
                'totalCount' => $total,
                'pagination' => ['defaultPageSize' => 15],
            ]);
        } elseif ($table) {
            $query = (new Query())->from("{{%$table}}");
            $model->value = $query->createCommand()->sql;
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => ['defaultPageSize' => 15],
            ]);
        } else
            $dataProvider = null;

        if (Yii::$app->request->get('save') && $model->load(Yii::$app->request->get()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Form models.
     * @return mixed
     */
    public function actionDownload($id)
    {
        $model = Sql::findOne($id);
        list($select, $request) = preg_split('#\sfrom\s#iu', $model->value);
        $dataProvider = new SqlDataProvider([
            'sql' => $model->value,
            'pagination' => false,
        ]);

        return $this->render('download', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
}
