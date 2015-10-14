<?php

namespace app\admin\controllers;

use app\admin\models\Auth;
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

    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();

        $data = [
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ];
        /* @var $auth Auth */
        $auth = Auth::findOne($data);

        if (!$auth)
            (new Auth(['user_id' => Yii::$app->user->id?:1] + $data))->save();

        VarDumper::dump($client, 10, 1);
        die;
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
