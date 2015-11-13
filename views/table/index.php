<?php

use ra\admin\helpers\RA;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model yii\db\ActiveRecord */

$this->title = $model->name ? $model->name : $module->name;
if (Yii::$app->request->get('q')) $this->title .= ': поиск "' . Yii::$app->request->get('q') . '"';
require_once(__DIR__ . '/_breadcrumbs.php');


$moduleColumns = empty($module->settings['columns']) || !empty($sortMode) ? [
    ['value' => function () {
        return '';
    }]
] : $module->settings['columns'];
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


                <?php echo Html::a('<i class="fa fa-sort-amount-asc"></i>', [
                    'index', 'url' => Yii::$app->request->get('url'), 'id' => Yii::$app->request->get('id'), 'sortMode' => empty($sortMode)
                ], ['class' => 'btn btn-theme02 tooltips', 'title' => Yii::t('ra', !empty($sortMode) ? 'Disable Sort Mode' : 'Enable Sort Mode')]) ?>
            </div>

            <h4>
                <span>
                    <i class="fa fa-angle-right"></i>
                    <? if ($model->parent_id) echo Html::a('<i class="fa fa-chevron-left"></i>', ['index', 'url' => RA::module($model->module_id), 'id' => $model->parent_id], ['class' => 'hide']) ?>
                </span> <?= Html::encode($this->title) ?> <? if ($model->id) echo Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id]) ?>
            </h4>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['data-sort' => !empty($sortMode) ? Url::to(['move']) : false],
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
                                    ['create', 'url' => RA::module($model->module_id), 'parent_id' => $model->id], [
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
                            }
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
