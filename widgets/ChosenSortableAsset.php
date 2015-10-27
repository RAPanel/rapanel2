<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 07.09.2015
 * Time: 16:06
 */

namespace ra\admin\widgets;


use yii\web\AssetBundle;

class ChosenSortableAsset extends AssetBundle
{
    public $sourcePath = '@ra/admin/widgets/assets';

    public $js = [
        'jquery-chosen-sortable.js'
    ];
}