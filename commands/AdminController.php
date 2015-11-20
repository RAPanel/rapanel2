<?php

namespace ra\admin\commands;

use Yii;
use yii\helpers\FileHelper;

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

        $this->mkDir([
            "../assets" => "0777",
            "../image" => "0777",
            "../source" => "0777",
            "../theme" => "0777",
        ]);

        $this->copyFile([
            "web/.htaccess" => "../.htaccess",
            "web/favicon.ico" => "../favicon.ico",
        ]);

        $this->addRunFile('../index.php');

        $this->runSqlFile(Yii::getAlias('@ra/admin/data/ra.sql'));

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

    /**
     * Create directories listed in the extra section with $permission.
     * @param array $paths the paths (keys) and the corresponding permission octal strings (values)
     */
    public static function mkDir(array $paths)
    {
        foreach ($paths as $path => $permission) {
            if (is_numeric($path) && $permission && is_string($permission)) {
                $path = $permission;
                $permission = 0777;
            }
            echo "mkdir('$path', $permission)...";
            if (FileHelper::createDirectory($path, $permission ?: 0777, true))
                echo "done.\n";
            else
                echo "dir can`t create.\n";

        }
    }

    /**
     * Copy file.
     * @param array $paths the files from (keys) and the files to copy (values)
     */
    public static function copyFile(array $paths)
    {
        foreach ($paths as $from => $to)
            if (is_file($from) && !file_exists($to) && copy($from, $to))
                echo "copy($from, $to)...done.\n";
    }

    /**
     * Insert run script.
     */
    public static function addRunFile()
    {
        $configs = func_get_args();
        foreach ($configs as $config) {
            if (!file_exists($config)) {
                $dir = Yii::getAlias('@app');
                $dir = str_replace(dirname($dir), '', $dir);
                $content = "<?php
                require(__DIR__ . \"" . $dir . "/web/index.php\");";
                file_put_contents($config, $content);
                echo "FileCreation({$config})...done";
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