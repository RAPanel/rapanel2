<?php

namespace app\admin\widgets\foundation;

use Yii;
use yii\web\AssetBundle;

class FoundationJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/foundation/js/foundation';
    public $initializeScript = false;

    public $css = [];

    public $js = [
        'foundation.js',
    ];

    public $depends = [
        '\yii\web\JqueryAsset',
        'app\admin\widgets\foundation\ModernizrAsset',
    ];
}
