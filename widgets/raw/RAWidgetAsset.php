<?php

namespace ra\admin\widgets\raw;

use yii\web\AssetBundle;


/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 10.09.2015
 * Time: 17:14
 */
class RAWidgetAsset extends AssetBundle
{
    public $sourcePath = '@ra/admin/widgets/raw/assets';

    public $css = [
        'rapanel.css',
    ];

    public $js = [
        'rapanel.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}