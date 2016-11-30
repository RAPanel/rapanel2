<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 14.04.2015
 * Time: 11:19
 */

namespace ra\admin\assets;


use yii\web\AssetBundle;

class CropperAsset extends AssetBundle
{

    public $sourcePath = '@bower/cropper/dist';

    public $css = [
        'cropper.min.css',
    ];

    public $js = [
        'cropper.min.js',
    ];

}