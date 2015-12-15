<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 16.09.2015
 * Time: 21:53
 */

namespace ra\admin\widgets\responsiveFilemanager;


use Yii;
use yii\web\AssetBundle;

class ResponsiveFilemanagerAsset extends AssetBundle
{
    public $sourcePath = '@ra/admin/widgets/responsiveFilemanager/assets';

    public $css = [
    ];

    public $js = [
    ];

    public $depends = [
    ];

    public function publish($am)
    {
        parent::publish($am);
        $filename = '/.htaccess';
        if (!file_exists($this->basePath . $filename))
            copy(Yii::getAlias($this->sourcePath) . $filename, $this->basePath . $filename);
    }

}