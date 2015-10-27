<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 14.04.2015
 * Time: 11:19
 */

namespace ra\admin\assets;


use yii\web\AssetBundle;

class DropZoneAsset extends AssetBundle
{

    public $sourcePath = '@bower/dropzone/dist/min';

    public $css = [
        'dropzone.min.css',
    ];

    public $js = [
        'dropzone.min.js',
    ];

}