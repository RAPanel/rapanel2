<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 21.10.2015
 * Time: 22:30
 */

namespace app\admin\controllers;


use app\models\LoginForm;
use Yii;
use yii\web\Controller;

class AuthController extends Controller
{

    /**
     * Display login page
     */
    public function actionLogin()
    {
        /** @var \app\admin\models\forms\LoginForm $model */
        // load post data and login
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login(Yii::$app->getModule("user")->loginDuration)) {
            return $this->goBack(Yii::$app->getModule("user")->loginRedirect);
        }
        // render
        return $this->render('login', [
            'model' => $model,
        ]);
    }

}