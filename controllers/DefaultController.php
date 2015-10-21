<?php

namespace app\admin\controllers;

use app\admin\helpers\RA;
use app\admin\models\forms\LoginForm;
use app\admin\models\UserAuth;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class DefaultController extends AdminController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],]);
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }


    /**
     * Display login page
     */
    public function actionLogin()
    {
        $this->layout = 'lock';

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login(RA::config('user')['loginDuration'])) {
            return $this->goBack(RA::config('user')['loginRedirect']);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Log user out and redirect
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        $logoutRedirect = RA::config('user')['logoutRedirect'];
        if ($logoutRedirect === null)
            return $this->goHome();
        else
            return $this->redirect($logoutRedirect);
    }

    /**
     * @param $client \yii\authclient\clients\YandexOAuth
     */
    public function onAuthSuccess($client)
    {
        $data = [
            'source' => $client->getId(),
            'source_id' => $client->userAttributes['id'],
        ];
        $auth = UserAuth::findOne($data);
        if (!$auth) $auth = new UserAuth($data);
        $auth->setAttributes([
            'user_id' => Yii::$app->user->id ?: ($auth ? $auth->user_id : 1),
            'source_attributes' => serialize($client->accessToken->params),
        ]);
        $auth->save(false);

        return $this->redirect(['success']);
    }

    public function actionSuccess()
    {
        return $this->renderContent('<h2>Спасибо за авторизацию</h2>');
    }

    public function actionIndex()
    {
        return $this->redirect(['module/index']);
        return $this->render('index');
    }

    public function actionInstall()
    {

        $data = [
            'app\admin\models\UserRole' => [
                ['id' => 1, 'name' => 'Admin', 'can_admin' => 1,],
                ['id' => 2, 'name' => 'User',],
            ],
            'app\admin\models\User' => [
                ['id' => 1, 'username' => 'semyonchick', 'email' => 'semyonchick@gmail.com', 'password' => 230987, 'role_id' => 1],
            ],
        ];

        foreach ($data as $key => $value) {
            foreach ($value as $row) {
                /** @var \yii\db\ActiveRecord $model */
                $model = new $key();
                if ($model::find()->where($row)->exists()) continue;
                $model->setAttributes($row, false);
                $model->save(false);
            }
        }
    }

    public function actionUpdate()
    {
        $dir = Yii::getAlias('@app');

        if (Yii::$app->request->isPost) {
            echo `php-cli {$dir}/composer.phar self-update --working-dir={$dir}/ --no-progress`;
            echo `php-cli {$dir}/composer.phar update -o --working-dir={$dir}/ --no-progress`;
            return $this->refresh();
        }

        return $this->render('update', compact('dir'));
    }

    public function actionFileManager()
    {
        $dir = Yii::getAlias('@webroot/source');
        if (!file_exists($dir)) mkdir($dir);
        return $this->render('fileManager');
    }
}
