<?php

namespace app\admin\assets;

use Yii;
use yii\web\AssetBundle;

class TagsInputAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-tags-input';

    public $css = [
        'jquery.tagsinput.css',
    ];

    public $depends = [
        '\yii\web\JqueryAsset',
        '\yii\jui\JuiAsset',
    ];

    public function init()
    {
        parent::init();

        $this->js = [
            'jquery.tagsinput' . (YII_DEBUG ? '' : '.min') . '.js'
        ];
    }
}
