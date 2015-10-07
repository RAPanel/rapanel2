<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.09.2015
 * Time: 10:45
 *
 * @var $data \app\admin\models\Character
 */


use yii\helpers\Html;
use yii\helpers\Inflector;

echo Html::beginTag('div', ['class' => 'form-group']);

$id = 'character' . $key;
$name = "{$attribute}[{$key}][value]";

echo Html::activeHiddenInput($model, "{$attribute}[{$key}][page_id]", ['value' => $model->id]);
echo Html::activeHiddenInput($model, "{$attribute}[{$key}][character_id]", ['value' => $data->id]);
echo Html::activeHiddenInput($model, $name);

$label = Yii::t('app/character', Inflector::camel2words($data->url));
if ($data['type'] == 'boolean') {
    echo Html::activeCheckbox($model, $name, ['label' => $label]);
} elseif ($data['type'] == 'textarea') {
    echo Html::label($label, $id);
    echo Html::activeTextarea($model, $name, ['id' => $id, 'class' => 'form-control']);
} else {
    $params = [];
    if ($data['type'] == 'price') {
        $params['pattern'] = '\d+(,\d{2})?';
        $data['type'] = 'number';
    } elseif ($data['type'] == 'checkbox') {
        $data['value'] = '1';
    }
    echo Html::label($label, $id);
    echo Html::activeInput($data['type'], $model, $name, ['id' => $id, 'class' => 'form-control'] + $params);
}
echo Html::endTag('div');