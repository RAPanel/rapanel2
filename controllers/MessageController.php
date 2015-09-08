<?php

namespace app\admin\controllers;

use app\admin\models\MessageTranslate;
use Yii;
use app\admin\models\Message;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Message::find()->joinWith('messageTranslate'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSave()
    {
        extract(Yii::$app->request->post());
        return Yii::$app->db->createCommand()->update(MessageTranslate::tableName(), compact('translation'), compact('id', 'language'))->execute();
    }

}
