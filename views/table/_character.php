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
$value = Html::getAttributeValue($model, $name);

echo Html::activeHiddenInput($model, "{$attribute}[{$key}][page_id]", ['value' => $model->id]);
echo Html::activeHiddenInput($model, "{$attribute}[{$key}][character_id]", ['value' => $data->id]);
echo Html::activeHiddenInput($model, $name, ['value' => is_array($value) ? implode(',', $value) : $value, 'id' => false]);

$label = Yii::t('app/character', Inflector::camel2words($data->url));
if ($data['type'] == 'boolean') {
    echo Html::activeCheckbox($model, $name, ['label' => $label]);
} elseif ($data['type'] == 'textarea') {
    echo Html::label($label, $id);
    echo Html::activeTextarea($model, $name, ['id' => $id, 'class' => 'form-control']);
} elseif ($data['type'] == 'extend') {
    echo Html::label($label, $id);
    $filter = [];
    foreach ($data['filter'] as $key => $row)
        if ($row) $filter[$key] = $row - 1;
    echo \nex\chosen\Chosen::widget([
        'id' => $id,
        'model' => $model,
        'attribute' => $name,
        'items' => \yii\helpers\ArrayHelper::map($model::findActive($data['module'], $filter)->select(['id', 'name'])->asArray()->all(), 'id', 'name'),
        'clientOptions' => [
            'search_contains' => true,
            'single_backstroke_delete' => false,
        ],
        'options' => [
            'class' => 'form-control',
            'size' => $data['multi'] ? 3 : 1,
        ],
        'multiple' => $data['multi'],
    ]);
} else {
    $params = [];
    if ($data['type'] == 'price') {
        $params['pattern'] = '\d+(,\d{2})?';
        $data['type'] = 'number';
        $params['step'] = '0.01';
    } elseif ($data['type'] == 'checkbox') {
        $params['value'] = '1';
    }
    echo Html::label($label, $id);
    echo Html::activeInput($data['type'], $model, $name, ['id' => $id, 'class' => 'form-control'] + $params);
}
echo Html::endTag('div');