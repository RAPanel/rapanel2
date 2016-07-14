<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace ra\admin\widgets\siteWidget;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SiteAsset extends AssetBundle
{
    public $sourcePath = '@ra/admin/widgets/siteWidget/assets';

    public $css = [
        'main.min.css',
    ];

    public $js = [
        'admin.js',
        'main.min.js',
        'svg4everybody.min.js',
    ];

    public $jsOptions = [
        'position'=>View::POS_BEGIN,
    ];

    public $depends = [
    ];

    public static function register($view)
    {
        $object = $view->registerAssetBundle(get_called_class());
        $view->registerJs("let adminTheme='{$object->baseUrl}/'", View::POS_HEAD);
        return $object;
    }
}
