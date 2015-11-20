<?php

namespace ra\admin\controllers;

use ra\admin\models\Sql;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
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
            $dataProvider = new SqlDataProvider([
                'sql' => $model->value,
                'totalCount' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM ' . $request)->queryScalar(),
            ]);
        } elseif ($table) {
            $query = (new Query())->from("{{%$table}}");
            $model->value = $query->createCommand()->sql;
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
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
    public function actionQuery($table = null)
    {
        $this->title = $table ?: 'Выберите таблицу';
        $dataProvider = $table ? new ActiveDataProvider([
            'query' => (new Query())->from($table),
        ]) : null;

        return $this->render('index', [
            'table' => $table,
            'dataProvider' => $dataProvider,
        ]);
    }
}
