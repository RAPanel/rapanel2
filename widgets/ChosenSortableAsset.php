<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 07.09.2015
 * Time: 16:06
 */

namespace app\admin\widgets;


use yii\web\AssetBundle;

class ChosenSortableAsset extends AssetBundle
{
    public $sourcePath = '@app/admin/widgets/assets';

    public $js = [
        'jquery-chosen-sortable.js'
    ];
}