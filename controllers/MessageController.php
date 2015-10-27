<?php

namespace ra\admin\controllers;

use ra\admin\models\Message;
use ra\admin\models\MessageTranslate;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends AdminController
{

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
