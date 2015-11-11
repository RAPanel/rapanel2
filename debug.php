<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 02.09.2015
 * Time: 16:43
 */

$debug = true;

if (in_array(php_uname('n'), ['localhost', 'devhost'])) {
    $debug = true;
    defined('YII_ENV') or define('YII_ENV', 'dev');
    defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);
}

if (!empty($debug)) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    ini_set('html_errors', true);

    defined('YII_DEBUG') or define('YII_DEBUG', true);
}