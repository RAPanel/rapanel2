<?php

namespace app\admin\controllers;

use app\admin\models\UserAuth;
use app\admin\models\UserKey;
use app\components\YMPA;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;

class DefaultController extends Controller
{
    public $layout = '@admin/views/layout/main.php';


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
        $y = new YMPA();
        $result = $y->campaigns(21286164)->offers()->get(['pageSize' => 1000]);
//        $result = $y->campaigns(21286164)->offers()->post(['models'=>[4980633, 1001896, 7768458, 10433914]])
        VarDumper::dump($result, 10, 1);

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
