<?php

namespace ra\admin\widgets\foundation;

use Yii;

class FoundationAsset extends FoundationJsAsset
{
    public $initializeScript = true;

    public $css = [
        'css/normalize.css',
        'css/foundation.css'
    ];
}
