<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.09.2015
 * Time: 0:07
 *
 * @var app\models\Photo $data
 */
use app\admin\helpers\RA;
use yii\helpers\Html;

if (isset($index)) $data->sort_id = $index;
else $index = $data->sort_id;

$model = $data->page;
$name = "photos[{$index}]";

?>
<div class="image col-md-<?= $index ? 6 : 12 ?>" id="photo<?= $data->id ?>">
    <div class="thumbnail">
        <?= Html::activeHiddenInput($model, "{$name}[id]", ['value' => $data->id]) ?>
        <?= Html::activeHiddenInput($model, "{$name}[sort_id]", ['value' => $index]) ?>
        <div class="row">
            <div class="col-md-5 photo">
                <?= Html::img($data->getFile(), ['class' => "img-responsive"]) ?>
            </div>
            <div class="col-md-7 right text form-group">
                <div class="row">
                    <div class="col-sm-6 type"><?=
                        Html::activeDropDownList($model, "{$name}[type]", RA::dropDownList(['main']), ['class' => 'form-control', 'value' => $data->type]) ?></div>
                    <div class="col-sm-4 size"><span class="width"><?= $data->width ?></span> x <span
                            class="height"><?= $data->height ?></span></div>
                    <div class="col-sm-2"><?=
                        Html::button('&times;', ['class' => 'close pull-right']) ?></div>
                </div>
                <br>

                <div>
                    <?= Html::activeTextarea($model, "{$name}[about]", ['rows' => 2, 'class' => 'form-control', ['value' => $data->about]]) ?>
                </div>
                <div class="name"></div>
            </div>
        </div>
    </div>
</div>