<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 04.09.2015
 * Time: 10:45
 *
 * @var $data \app\models\Character
 */


use yii\helpers\Html;
use yii\helpers\Inflector;

echo Html::beginTag('div', ['class' => 'form-group']);

echo Html::activeHiddenInput($model, "pageCharacters[{$data->id}][page_id]", ['value' => $model->id]);
echo Html::activeHiddenInput($model, "pageCharacters[{$data->id}][character_id]", ['value' => $data->id]);

$label = Yii::t('app/character', Inflector::camel2words($data->url));
if ($data['type'] == 'boolean') {
    echo Html::label(Html::activeCheckbox($model, "pageCharacters[{$data->id}][value]", ['checked' => !empty($value), 'label'=>$label]));
} elseif($data['type'] == 'textarea') {
    echo Html::label($label, 'character' . $data->id);
    echo Html::activeTextarea($model, "pageCharacters[{$data->id}][value]", ['id' => 'character' . $data->id, 'value' => isset($value) ? $value : null, 'class' => 'form-control']);
} else {
    $params = [];
    if($data['type'] == 'price'){
        $params['pattern'] = '\d+(,\d{2})?';
        $data['type'] = 'number';
    }
    echo Html::label($label, 'character' . $data->id);
    echo Html::activeInput($data['type'], $model, "pageCharacters[{$data->id}][value]", ['id' => 'character' . $data->id, 'value' => isset($value) ? $value : null, 'class' => 'form-control'] + $params);
}
echo Html::endTag('div');