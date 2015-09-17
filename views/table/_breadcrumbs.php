<?php

/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 10.09.2015
 * Time: 21:58
 */


use app\admin\helpers\RA;

$this->params['breadcrumbs'][] = ['label' => Yii::t('ra/view', 'Modules'), 'url' => ['module/index']];
if (isset($model->module_id) && RA::module($model->module_id, 'name') != $this->title)
    $this->params['breadcrumbs'][$model->module_id] = ['label' => RA::module($model->module_id, 'name'), 'url' => ['table/index', 'url' => RA::module($model->module_id)]];

if ($model->parent && $model->parent->hasMethod('parents')) {
    foreach (array_reverse($model->parent->parents()->andWhere(['>', 'level', 0])->all()) as $row)
        $this->params['breadcrumbs'][$row->id] = ['label' => $row->name, 'url' => ['index', 'url' => RA::module($row->module_id), 'id' => $row->id]];
    $this->params['breadcrumbs'][$model->parent->id] = ['label' => $model->parent->name, 'url' => ['index', 'url' => RA::module($model->parent->module_id), 'id' => $model->parent->id]];
}

if ($model->is_category && $model->name != $this->title)
    $this->params['breadcrumbs'][$model->id] = ['label' => $model->name, 'url' => ['index', 'url' => RA::module($model->module_id), 'id' => $model->id]];


$this->params['breadcrumbs'][] = $this->title;
