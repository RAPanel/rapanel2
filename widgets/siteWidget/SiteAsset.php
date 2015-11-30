<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace ra\admin\widgets\siteWidget;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SiteAsset extends AssetBundle
{
    public $sourcePath = '@ra/admin/widgets/siteWidget/assets';

    public $css = [
    ];

    public $js = [
        'admin.js'
    ];

    public $depends = [
    ];
}
