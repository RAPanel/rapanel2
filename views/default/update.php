<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 15.09.2015
 * Time: 22:36
 */

use yii\helpers\Html;

$this->params['breadcrumbs'][] = 'Обновление системы';

$dir = Yii::getAlias('@app');

$output = `php-cli {$dir}/composer.phar --version --working-dir={$dir}/`;
echo "<pre>$output</pre>";

$output = `php-cli {$dir}/composer.phar show -i --working-dir={$dir}/`;
echo "<pre>$output</pre>";

echo Html::beginForm() . Html::submitButton('Обновить систему', ['class'=>'btn pull-right']) . Html::endForm();