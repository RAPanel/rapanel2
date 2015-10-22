<?php

namespace app\admin\commands;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 22.10.2015
 * Time: 9:24
 */
class AdminController extends \yii\console\Controller
{
    public function actionInstall()
    {
        $data = [
            'app\admin\models\UserRole' => [
                ['id' => 1, 'name' => 'Admin', 'can_admin' => 1,],
                ['id' => 2, 'name' => 'User',],
            ],
            'app\admin\models\User' => [
                ['id' => 1, 'status' => 1, 'username' => 'semyonchick', 'email' => 'semyonchick@gmail.com', 'password' => '$2y$13$PNXd8rZ1/k4avlEQE.IlJOFqj81jJLiyfbw6pWqthODGxJWR61bUC', 'role_id' => 1],
                ['id' => 3, 'status' => 1, 'username' => 'valeryrere', 'email' => 'mail@rere-design.ru', 'password' => '$2y$13$Ff.UTgG4SbxYMsO.kIz5NefS9p60NaGvSknntHu16KdBzLM.pJtyO', 'role_id' => 1],
                ['id' => 4, 'status' => 1, 'username' => 'denart89', 'email' => 'denart89@gmail.com', 'password' => '$2y$13$/JlaJ1KhWIlPPtFXJOjVU.goTnn2KtZUuRtdSzt0Onl/4DQq4fVSC', 'role_id' => 1],
                ['id' => 5, 'status' => 1, 'username' => 'last_leaf', 'email' => 'v.a.elokhova@gmail.com', 'password' => '$2y$13$zlmonGZ3z/M.aCHl9Qb9Nu76CkYA9WY1OAxq0YNw5W6lojf6MRA0O', 'role_id' => 1],
            ],
        ];

        foreach ($data as $key => $value) {
            foreach ($value as $row) {
                /** @var \yii\db\ActiveRecord $model */
                $model = new $key();

                $query = $model::find();
                foreach ($model->primaryKey() as $pk)
                    $query->andWhere([$pk => $row[$pk]]);
                if ($query->exists()) continue;

                $model->setAttributes($row, false);
                $model->save(false);
            }
        }
    }
}