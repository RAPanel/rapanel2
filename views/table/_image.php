<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.09.2015
 * Time: 0:07
 *
 * @var ra\admin\models\Photo $data
 */
use ra\admin\helpers\RA;
use yii\helpers\Html;

if (isset($index)) $data->sort_id = $index;
else $index = $data->sort_id;

$model = $data->page;
$name = "photos[{$index}]";

$types = array_map('trim', explode(',', RA::moduleSetting($model->module_id, 'photosTypes')));
$types = array_diff($types, ['']);
?>

<div class="image" id="photo<?= $data->id ?>">

<a data-toggle="modal" data-target="#pm<?= $data->id ?>" data-sort="<?= $index ?>">
    <img class="img-responsive" src="<?= $data->getHref('150x150') ?>"></a>

<div id="pm<?= $data->id ?>" class="fade modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <?= $model->name ?>
            </div>

            <div class="modal-body">
                <div class="row">
                    <?= Html::activeHiddenInput($model, "{$name}[id]", ['value' => $data->id]) ?>
                    <?= Html::activeHiddenInput($model, "{$name}[sort_id]", ['value' => $index]) ?>
                    <div class="col-md-7" style="max-height: 400px">
                        <img class="img-responsive" src="<?= $data->getFile() ?>"></button>
                    </div>
                    <div class="col-md-5 form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-7"><?=
                                Html::activeDropDownList($model, "{$name}[type]", RA::dropDownList(array_merge_recursive(['main'], $types)), ['class' => 'form-control col-sm-7', 'value' => $data->type]) ?></div>
                            <div class="col-sm-5" style="line-height: 2.4">
                                <span class="width"><?= $data->width ?></span>x<span
                                    class="height"><?= $data->height ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12"><?=
                                Html::activeTextarea($model, "{$name}[about]", ['rows' => 2, 'class' => 'form-control', 'value' => $data->about]) ?></div>
                        </div>
                        <?= Html::button('ok', ['class' => 'btn btn-primary pull-right', 'data-dismiss' => "modal", 'aria-hidden' => "true"]) ?>
                        <?= Html::button('delete', ['class' => 'btn btn-danger remove']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>