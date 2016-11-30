<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.09.2015
 * Time: 0:07
 *
 * @var ra\admin\models\Photo $data
 */
use yii\helpers\Html;

if (isset($index)) $data->sort_id = $index;
else $index = $data->sort_id;

$model = $data->page;
$name = "photos[{$index}]";
?>

<div class="image" id="photo<?= $data->id ?>">

    <a data-toggle="modal" data-target="#pm<?= $data->id ?>" data-sort="<?= $index ?>">
        <img class="img-responsive" src="<?= $data->getHref('150x150') ?>"><span
            class="label label-info"><?= $data->type ?></span></a>

    <div id="pm<?= $data->id ?>" class="fade modal" role="dialog" tabindex="-1">
        <?= Html::activeHiddenInput($model, "{$name}[id]", ['value' => $data->id]) ?>
        <?= Html::activeHiddenInput($model, "{$name}[sort_id]", ['value' => $index]) ?>
        <div class="dataSerialized hide"><?= \yii\helpers\Json::encode([
                'index' => $index,
                'src' => 'src="' . $data->getFile() . '"',
                'name' => Html::encode($model->name),
                'width' => $data->width,
                'height' => $data->height,
                'type' => $data->type,
                'about' => Html::encode($data->about),
                'cx' => isset($data->cropParams['x'])?$data->cropParams['x']:'',
                'cy' => isset($data->cropParams['y'])?$data->cropParams['y']:'',
                'cw' => isset($data->cropParams['width'])?$data->cropParams['width']:'',
                'ch' => isset($data->cropParams['height'])?$data->cropParams['height']:'',
            ]) ?></div>
    </div>
</div>