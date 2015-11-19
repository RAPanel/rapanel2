<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 19.11.2015
 * Time: 10:10
 */

namespace ra\admin;


class Installer extends \yii\composer\Installer
{

    public static function postCreateProject($event)
    {
        parent::postCreateProject($event);
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
            mkdir($path, $permission ?: 0777, true);
            echo "mkdir('$path', $permission)...";
            if (is_dir($path)) {
                chmod($path, octdec($permission));
                echo "done.\n";
            } else {
                echo "dir can`t create.\n";
            }
        }
    }

    /**
     * Copy file.
     * @param array $paths the files from (keys) and the files to copy (values)
     */
    public static function copyFile(array $paths)
    {
        foreach ($paths as $from => $to) {
            if (is_file($from))
                copy($from, $to);
        }
    }

    /**
     * Insert run script.
     */
    public static function addRunFile()
    {
        $configs = func_get_args();
        foreach ($configs as $config) {
            if (is_file($config)) {
                $dir = str_replace(dirname(__DIR__), '', __DIR__);
                $content = "<?php
require(__DIR__ . \"{$dir}/web/index.php\");";
                file_put_contents($config, $content);


            }
        }
    }
}