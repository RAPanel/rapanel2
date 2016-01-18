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
use yii\web\Cookie;

class AdminController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'returnUrl' => [
                'class' => 'ra\admin\filter\ReturnUrl',
                'returnUrlParam' => 'return' . ucfirst($this->id),
                'only' => ['index'],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
                'denyCallback' => function () {
                    Yii::$app->getUser()->setReturnUrl(Yii::$app->request->url);
                    return Yii::$app->controller->redirect(['default/login']);
                }
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->response->cookies->add(new Cookie(['name' => 'canAdmin', 'value' => 1, 'expire' => time() + 60 * 60 * 24 * 365]));
        return parent::beforeAction($action);
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