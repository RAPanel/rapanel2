<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 16.09.2015
 * Time: 22:22
 */

use ra\admin\widgets\responsiveFilemanager\ResponsiveFilemanagerAsset;

$this->params['breadcrumbs'][] = 'Файловый менеджер';

$data = ResponsiveFilemanagerAsset::register($this);

$js = <<<JS
$(window).resize(function(){
    $('#fileManager').css('height', $('#sidebar').outerHeight()-60);
}).resize();
JS;
$this->registerJs($js);
?>
<div class="row">
    <iframe id="fileManager" src="<?= $data->baseUrl ?>/dialog.php" frameborder="0" width="100%" height="500"
            style="margin-bottom:-25px"></iframe>
</div>