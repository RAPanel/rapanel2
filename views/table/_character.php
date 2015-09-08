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

echo Html::beginTag('div', ['class' => 'form-group']);

echo Html::activeHiddenInput($model, "pageCharacters[{$data->id}][page_id]", ['value' => $model->id]);
echo Html::activeHiddenInput($model, "pageCharacters[{$data->id}][character_id]", ['value' => $data->id]);

if ($data['type'] == 'boolean') {
    echo Html::label(Html::activeCheckbox($model, "pageCharacters[{$data->id}][value]", ['checked' => !empty($value)]) . "\n" . Yii::t('rere.character', $data->url));
} else {
    echo Html::label(Yii::t('rere.character', $data->url), 'character' . $data->id);
    echo Html::activeInput($data->type, $model, "pageCharacters[{$data->id}][value]", ['id' => 'character' . $data->id, 'value' => isset($value) ? $value : null, 'class' => 'form-control']);
}
echo Html::endTag('div');