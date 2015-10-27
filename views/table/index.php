<?php

use ra\admin\helpers\RA;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model yii\db\ActiveRecord */

$this->title = $model->name ? $model->name : $module->name;
if(Yii::$app->request->get('q')) $this->title .= ': поиск "'.Yii::$app->request->get('q').'"';
require_once(__DIR__ . '/_breadcrumbs.php');


$moduleColumns = empty($module->settings['columns']) ? [] : $module->settings['columns'];
//$columns = $relations = [];
//$i = 0;
//if (count($moduleColumns))
//    foreach ($model->getTableSchema()->columns as $key => $value) {
//        if (!$model->isAttributeSafe($key) || $key == 'module_id') continue;
//        $format = 'text';
//        if (strpos($key, '_id')) {
//            $name = str_replace('_id', '', $key);
//            $get = 'get' . ucfirst($name);
//            if (method_exists($model, $get)) {
//                $modelClass = $model->$get()->modelClass;
//                /** @var yii\db\ActiveRecord $class */
//                $class = new $modelClass;
//                foreach (array_keys($class->attributes) as $attribute)
//                    if (in_array($attribute, ['username', 'name', 'value']))
//                        break;
//                if (isset($attribute)) {
//                    $key = "{$name}.{$attribute}";
//                    $relations[$name] = function ($query) use ($name, $class) {
//                        $query->from(["{$name}_alias" => $class::tableName()]);
//                    };
//                }
//            }
//        } else {
//            if (in_array($value->type, ['timestamp', 'date', 'datetime']))
//                $format = 'date';
//            if (strpos($value->type, 'int') !== false && $value->size == 1)
//                $format = 'boolean';
//            if (strpos($value->type, 'int') !== false && $value->size == 11)
//                $format = 'integer';
//        }
//
//        if (empty($moduleColumns) || in_array($key, $moduleColumns)) {
//            $columns[empty($moduleColumns) ? $i++ : current(array_keys($moduleColumns, $key))] = [
//                'attribute' => $key,
//                'label' => Yii::t('ra', mb_convert_case(str_replace(['_', '.'], ' ', $key), MB_CASE_TITLE)),
//                'format' => $format,
//            ];
//            if (in_array($key, $moduleColumns)) unset($moduleColumns[array_search($key, $moduleColumns)]);
//        }
//    }
//
//ksort($columns);
//
//if (count($relations))
//    $dataProvider->query->joinWith($relations);

?>
<div class="page-index">
    <div class="row content-panel">
        <div class="col-lg-12">
            <div class="pull-right">
                <?
                $list = [];
                $list['file'] = ['create',
                    'url' => $module->url,
                    'parent_id' => Yii::$app->request->get('id'),
                    'is_category' => !empty($module->settings['hasChild']),
                ];
                if (!empty($module->settings['hasCategory']))
                    $list['folder'] = ['create', 'url' => $module->url, 'parent_id' => Yii::$app->request->get('id'), 'is_category' => 1];

                foreach ($list as $key => $url)
                    echo Html::a(' <i class="fa fa-plus"></i> &nbsp; <i class="fa fa-' . $key . '"></i>', $url, [
                            'class' => 'btn btn-theme03 tooltips',
                            'title' => Yii::t('ra', mb_convert_case('add ' . $key, MB_CASE_TITLE)),
                        ]) . "\n";
                ?>

                <?= Html::a('<i class="fa fa-cog"></i>', [
                    'module/update', 'id' => $module->id, 'back' => Yii::$app->request->url
                ], ['class' => 'btn btn-theme tooltips', 'title' => Yii::t('ra', 'Module Settings')]) ?>
            </div>

            <h4>
                <span>
                    <i class="fa fa-angle-right"></i>
                    <? if ($model->parent_id) echo Html::a('<i class="fa fa-chevron-left"></i>', ['index', 'url' => RA::module($model->module_id), 'id' => $model->parent_id], ['class' => 'hide']) ?>
                </span> <?= Html::encode($this->title) ?> <? if($model->id) echo Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id]) ?>
            </h4>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => \yii\helpers\ArrayHelper::merge([
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['style' => 'width:80px'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => ['style' => 'width:90px'],
                        'template' => '{status} {view} {update} {add}',
                        'buttonOptions' => [
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ],
                        'buttons' => [
                            'add' => function ($url, $model, $key) use ($module) {
                                return $model->is_category ? Html::a('<span class="glyphicon glyphicon-plus"></span>',
                                    ['create', 'url' => RA::module($model->module_id), 'parent_id' => $model->id, 'is_category' => !empty($module->settings['hasChild'])], [
                                        'title' => 'Добавить запись в ' . $model->name,
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                    ]) : false;
                            },
                            'status' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-toggle-' . ($model->status ? 'on' : 'off') . '"></i>', ['save', 'id' => $model->id, 'status' => !$model->status], [
                                    'data-toggle' => 'tooltip',
                                    'title' => ($model->status ? 'Скрыть' : 'Отобразить'),
                                    'class' => 'changeStatus pull-right',
                                ]);
                            }/*,
                            'view' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-eye></i>', ['save', 'id' => $model->id, 'status' => !$model->status], [
                                    'data-toggle' => 'tooltip',
                                    'title' => ($model->status ? 'Скрыть' : 'Отобразить'),
                                    'class' => 'changeStatus pull-right',
                                ]);
                            }*/
                        ],
                    ],
                    [
                        'attribute' => 'name',
                        'label' => 'Наименование',
                        'contentOptions' => ['style' => 'width:40%'],
                        'value' => function ($data) use ($module) {
                            $prev = '';
                            if ($data->is_category) {
                                if (!empty($module->settings['hasCategory'])) $prev .= '<i class="fa fa-folder"></i> ';
                                if (!empty($module->settings['hasChild'])) $prev .= '<i class="fa fa-file"></i> ';
                                return (method_exists($data, 'getPhoto') && $data->photo ? Html::img($data->photo->getHref('x35'), ['style' => 'margin: -8px;float: right;']) : '') .
                                Html::a($prev . $data->name, ['index', 'url' => $module->url, 'id' => $data->id]);
                            }
                            return $data->name;
                        },
                        'format' => 'raw',
                    ],
                ], \yii\helpers\ArrayHelper::merge($moduleColumns, [
                    [
                        'attribute' => 'created_at',
                        'label' => 'Создано',
                        'format' => 'date',
                        'contentOptions' => ['style' => 'width:81px'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => ['style' => 'width:25px'],
                        'template' => '{delete}',
                    ],
                ])),
            ]); ?>

        </div>
    </div>
</div>
