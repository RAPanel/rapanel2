<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 15.09.2015
 * Time: 22:36
 */

use yii\helpers\Html;

$this->params['breadcrumbs'][] = 'Обновление системы';

$output = `{$commands['version']}`;
echo "<pre>$output</pre>";

$output = `{$commands['info']}`;
echo "<pre>$output</pre>";

echo Yii::$app->formatter->asDatetime(filemtime($dir . '/composer.lock'), 'full');

echo Html::beginForm() . Html::submitButton('Обновить систему', ['class' => 'btn pull-right']) . Html::endForm();