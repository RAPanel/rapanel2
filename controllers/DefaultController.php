<?php

namespace app\admin\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public $layout = '@admin/views/layout/main.php';

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
}
