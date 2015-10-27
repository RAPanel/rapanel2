<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 14.04.2015
 * Time: 11:19
 */

namespace ra\admin\assets;


use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{

    public $sourcePath = '@bower/fontawesome/';

    public $css = [
        'css/font-awesome.min.css',
    ];

}