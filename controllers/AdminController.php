<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 03.09.2015
 * Time: 22:37
 */

namespace ra\admin\controllers;


use ra\admin\helpers\Text;
use Yii;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AdminController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionSave()
    {
        if (!method_exists($this, 'findModel'))
            throw new NotSupportedException('Method does not exist');
        /** @var ActiveRecord $model */
        $model = $this->findModel(Yii::$app->request->get('id'));
        $model->setAttributes(Yii::$app->request->get(), false);
        return $model->save(false, array_keys(Yii::$app->request->get()));
    }

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionSearch($url = null, $q = null)
    {
        if (!$q) return $this->redirect(['index'], $_GET);

        $class = '\\ra\\admin\\models\\' . ucfirst($this->id);

        /** @var \yii\db\ActiveRecord $model */
        $model = new $class;

        $or = ['or'];
        $lang = $q == preg_replace('#а-я#iu', '', $q) ? 'en' : 'ru';
//        var_dump($model->getTableSchema());die;
        foreach ($model->getTableSchema()->columns as $key => $column) {
            if (
                in_array($column->phpType, ['resource']) ||
                (in_array($column->phpType, ['integer']) && !is_numeric($q)) ||
                (!empty($column->enumValues) && $lang == 'ru')
            ) continue;
            $or[] = ['or', [$column->name => $q], ['like', $column->name, Text::search($q)]];
        }

        $query = $model::find()->where($or);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

}