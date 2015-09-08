<?php

use app\admin\helpers\RA;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model yii\db\ActiveRecord */

$this->title = $module->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rere.view', 'Modules'), 'url' => ['module/index']];

if ($model->hasMethod('parents') && $model->parent_id) {
    foreach (array_reverse($model->parents()->andWhere(['>', 'level', 0])->all()) as $row)
        $this->params['breadcrumbs'][] = ['label' => $row->name, 'url' => ['index', 'url' => RA::module($row->module_id), 'id' => $row->id]];
    $this->params['breadcrumbs'][] = $model->name;
} else $this->params['breadcrumbs'][] = $this->title;


$moduleColumns = empty($module->settings['columns']) ? [] : $module->settings['columns'];
$columns = $relations = [];
$i = 0;
foreach ($model->getTableSchema()->columns as $key => $value) {
    if (!$model->isAttributeSafe($key) || $key == 'module_id') continue;
    $format = 'text';
    if (strpos($key, '_id')) {
        $name = str_replace('_id', '', $key);
        $get = 'get' . ucfirst($name);
        if (method_exists($model, $get)) {
            $modelClass = $model->$get()->modelClass;
            /** @var yii\db\ActiveRecord $class */
            $class = new $modelClass;
            foreach (array_keys($class->attributes) as $attribute)
                if (in_array($attribute, ['username', 'name', 'value']))
                    break;
            if (isset($attribute)) {
                $key = "{$name}.{$attribute}";
                $relations[$name] = function ($query) use ($name, $class) {
                    $query->from(["{$name}_alias" => $class::tableName()]);
                };
            }
        }
    } else {
        if (in_array($value->type, ['timestamp', 'date', 'datetime']))
            $format = 'date';
        if (strpos($value->type, 'int') !== false && $value->size == 1)
            $format = 'boolean';
        if (strpos($value->type, 'int') !== false && $value->size == 11)
            $format = 'integer';
    }


    if (empty($moduleColumns) || in_array($key, $moduleColumns)) {
        $columns[empty($moduleColumns) ? $i++ : current(array_keys($moduleColumns, $key))] = [
            'attribute' => $key,
            'label' => Yii::t('rere.model', mb_convert_case(str_replace(['_', '.'], ' ', $key), MB_CASE_TITLE)),
            'value' => $key == 'name' && (!empty($module->settings['hasCategory']) || !empty($module->settings['hasChild'])) && ($format = 'html') ? function ($data) use ($key, $module) {
                return $data->is_category ? Html::a($data->$key, ['index', 'url' => $module->url, 'id' => $data->id]) : $data->$key;
            } : null,
            'format' => $format,
        ];
        if (in_array($key, $moduleColumns)) unset($moduleColumns[array_search($key, $moduleColumns)]);
    }
}

ksort($columns);

if (count($relations))
    $dataProvider->query->joinWith($relations);

?>
<div class="page-index">

    <!--    <h1>--><? //= Html::encode($this->title) ?><!--</h1>-->

    <p>
        <?= Html::a('', ['module/update', 'id' => $module->id, 'back' => Yii::$app->request->url], ['class' => 'btn btn-info glyphicon glyphicon-cog pull-right']) ?>

        <? if (!empty($module->settings['hasCategory'])): ?>
            <?= Html::a(Yii::t('rere.view', 'Create Category'), ['create', 'url' => $module->url, 'parent_id' => Yii::$app->request->get('id'), 'is_category' => 1], ['class' => 'btn btn-success']) ?>
        <? endif ?>

        <?= Html::a(Yii::t('rere.view', 'Create Element'), ['create',
            'url' => $module->url,
            'parent_id' => Yii::$app->request->get('id'),
            'is_category' => !empty($module->settings['hasChild']),
        ], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => array_merge(array_merge([
            [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width:80px'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:70px'],
                'template' => '{add} {view} {update}',
                'buttonOptions' => [
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                ],
                'buttons' => [
                    'add' => function ($url, $model, $key) use ($module) {
                        return Html::a('<span class="glyphicon glyphicon-plus"></span>',
                            ['create', 'url' => RA::module($model->module_id), 'parent_id' => $model->id, 'is_category' => !empty($module->settings['hasChild'])], [
                                'title' => 'Добавить запись в ' . $model->name,
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                            ]);
                    },
                ],
            ],
        ], $columns), \yii\helpers\ArrayHelper::merge($moduleColumns, [
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:25px'],
                'template' => '{delete}',
            ],
        ])),
    ]); ?>

</div>
