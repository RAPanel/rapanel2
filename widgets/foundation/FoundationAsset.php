<?php

namespace app\admin\widgets\foundation;

use Yii;
use yii\web\AssetBundle;

class FoundationAsset extends FoundationJsAsset
{
    public $initializeScript = true;

    public $css = [
        'css/normalize.css',
        'css/foundation.css'
    ];
}
