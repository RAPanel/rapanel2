<?php

namespace ra\admin\commands;
use Yii;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 22.10.2015
 * Time: 9:24
 */
class AdminController extends \yii\console\Controller
{

    public function actionUpdate()
    {
        $this->command('self-update');
        $this->command('update -o');
    }

    public function command($command)
    {
        $dir = \Yii::getAlias('@app');
        $php = PHP_BINDIR . '/php';
        echo `{$php} {$dir}/composer.phar {$command} --working-dir={$dir}/`;
    }

    public function actionInstall()
    {
//        $this->command('require rere/yii2-admin "dev-master"');

        var_dump($this->runSqlFile(Yii::getAlias('@ra/admin/data/ra.sql')));

        $data = [
            'ra\admin\models\UserRole' => [
                ['id' => 1, 'name' => 'Admin', 'can_admin' => 1,],
                ['id' => 2, 'name' => 'User',],
            ],
            'ra\admin\models\User' => [
                ['id' => 1, 'status' => 1, 'username' => 'semyonchick', 'email' => 'semyonchick@gmail.com', 'password' => '$2y$13$PNXd8rZ1/k4avlEQE.IlJOFqj81jJLiyfbw6pWqthODGxJWR61bUC', 'role_id' => 1],
                ['id' => 3, 'status' => 1, 'username' => 'valeryrere', 'email' => 'mail@rere-design.ru', 'password' => '$2y$13$Ff.UTgG4SbxYMsO.kIz5NefS9p60NaGvSknntHu16KdBzLM.pJtyO', 'role_id' => 1],
                ['id' => 4, 'status' => 1, 'username' => 'denart89', 'email' => 'denart89@gmail.com', 'password' => '$2y$13$/JlaJ1KhWIlPPtFXJOjVU.goTnn2KtZUuRtdSzt0Onl/4DQq4fVSC', 'role_id' => 1],
                ['id' => 5, 'status' => 1, 'username' => 'last_leaf', 'email' => 'v.a.elokhova@gmail.com', 'password' => '$2y$13$Vo0lztRy.i/pb94Oni/pLOEpXNCyicszCS/M1OfluwQE5yBXZUKnq', 'role_id' => 1],
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

                Yii::$app->db->createCommand()->insert($model::tableName(), $row)->execute();
            }
        }
    }

    public function runSqlFile($location)
    {
        //load file
        $commands = file_get_contents($location);

        //delete comments
        $lines = explode("\n", $commands);
        $commands = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && !$this->startsWith($line, '--')) {
                $commands .= $line . "\n";
            }
        }

        //convert to array
        $commands = explode(";", $commands);

        //run commands
        $total = $success = 0;
        foreach ($commands as $command) {
            if (trim($command)) {
                $success += (Yii::$app->db->createCommand($command)->execute() == false ? 0 : 1);
                $total += 1;
            }
        }

        //return number of successful queries and total number of queries found
        return array(
            "success" => $success,
            "total" => $total
        );
    }


// Here's a startsWith function
    public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}