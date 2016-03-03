<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.09.2015
 * Time: 10:45
 *
 * @var $data \ra\admin\models\Character
 */


use ra\admin\helpers\RA;
use yii\helpers\Html;
use yii\helpers\Inflector;

echo Html::beginTag('div', ['class' => 'form-group']);

$id = 'character' . $key;
$name = "{$attribute}[{$key}][value]";
$value = Html::getAttributeValue($model, $name);

echo Html::activeHiddenInput($model, "{$attribute}[{$key}][page_id]", ['value' => $model->id]);
echo Html::activeHiddenInput($model, "{$attribute}[{$key}][character_id]", ['value' => $data->id]);
echo Html::activeHiddenInput($model, $name, ['value' => is_array($value) ? RA::multiImplode(';;', $value) : $value, 'id' => false]);

$label = Yii::t('app\character', Inflector::camel2words($data->url)) . ' /<span>' . $data->url . '</span>';
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
    echo \ra\admin\widgets\chosen\Chosen::widget([
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
} elseif ($data['type'] == 'table') {
    echo Html::label($label, $id);
    echo Html::beginTag('table', ['style'=>'table-layout: fixed;width: 100%;']);
    {
        echo Html::beginTag('tr');
        {
            if ($data['filter']['firstColumn'])
                echo Html::tag('th', $data['filter']['firstColumn']);
            foreach ($data['filter']['column'] as $column) if($column)
                echo Html::tag('th', $column);
        }
        echo Html::endTag('tr');

        $i = 0;
        echo Html::beginTag('tr');
        {
            $name = $name . "[{$i}]";
            if ($data['filter']['firstColumn'])
                echo Html::tag('td', $data['filter']['unit']?:Html::activeInput($data['type'], $model, $name . "[{$n}]", ['class' => 'form-control']));
            foreach ($data['filter']['column'] as $n => $column) if($column)
                echo Html::tag('td', Html::activeInput($data['type'], $model, $name . "[{$n}]", ['class' => 'form-control']));
        }
        echo Html::endTag('tr');
    }
    echo Html::endTag('table');
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
    if ($data['multi']) {
        if (Html::getAttributeValue($model, $name))
            foreach (Html::getAttributeValue($model, $name) as $i => $row) if ($row) {
                echo Html::activeInput($data['type'], $model, $name . "[{$i}]", ['id' => $id, 'class' => 'form-control'] + $params);
            }
        $name .= '[]';
    }
    echo Html::activeInput($data['type'], $model, $name, ['id' => $id, 'value' => $data['multi'] ? '' : null, 'class' => 'form-control'] + $params);
    if ($data['multi']) {
        echo Html::button('add', ['onclick' => '$(this).prev(":input").clone().val("").insertBefore(this);']);
    }
}
echo Html::endTag('div');