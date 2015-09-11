<?php

namespace app\admin\widgets\adminTheme;

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 10.09.2015
 * Time: 17:14
 */
class AdminThemeAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/admin/widgets/adminTheme/assets';

    public $css = [
        '//fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&subset=latin,cyrillic',
        '//fonts.googleapis.com/css?family=Ruda:400,700,900',
        'font-awesome/css/font-awesome.css',
        'css/style.css',
        'css/style-responsive.css',
        'css/table-responsive.css',
    ];

    public $js = [
        'js/jquery.dcjqaccordion.2.7.js',
        'js/jquery.scrollTo.min.js',
        'js/jquery.nicescroll.js',
        'js/common-scripts.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\admin\assets\FontAwesomeAsset',
    ];
}