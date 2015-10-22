<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 03.09.2015
 * Time: 22:37
 */

namespace app\admin\controllers;


use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;

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

}