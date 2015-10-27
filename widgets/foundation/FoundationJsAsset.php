<?php

namespace ra\admin\widgets\foundation;

use Yii;
use yii\web\AssetBundle;

class FoundationJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/foundation/js';
    public $initializeScript = false;

    public $css = [];

    public $js = [
        'foundation.min.js',
    ];

    public $depends = [
        '\yii\web\JqueryAsset',
        'ra\admin\widgets\foundation\ModernizrAsset',
    ];
}
