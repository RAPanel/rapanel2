<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 10.09.2015
 * Time: 17:25
 */
use yii\helpers\Html;

?>

<aside>
    <div id="sidebar" class="nav-collapse " tabindex="5000" style="overflow: hidden; outline: none;">

        <?= Html::beginForm(['search'], 'get') ?>
        <div class="search" style="margin: 75px 10px 15px;position: relative;">
            <?= Html::submitButton('<i class="fa fa-search"></i>', ['class'=>'btn', 'style'=>'position:absolute;right:0']) ?>
            <? if(Yii::$app->request->get('url')) echo Html::hiddenInput('url', Yii::$app->request->get('url')) ?>
            <?= Html::input('search', 'q', Yii::$app->request->get('q'), ['class'=>'form-control', 'style'=>'padding-right:40px']) ?>
        </div>
        <?= Html::endForm() ?>

        <?
        $list = [];
        foreach (\app\admin\models\Module::find()->all() as $row)
            $list[] = ['label' => $row->name, 'url' => ['table/index', 'url' => $row->url]];

        echo \yii\widgets\Menu::widget([
            'activateParents' => true,
            'encodeLabels' => false,
            'submenuTemplate' => "\n<ul class='sub'>\n{items}\n</ul>\n",
            'options' => ['class' => 'sidebar-menu', 'id' => 'nav-accordion', 'style'=>'margin-top:15px'],
            'items' => [
                ['label' => '<i class="fa fa-dashboard"></i> <span>Состояние</span>', 'url' => ['default/index']],
                ['label' => '<i class="fa fa-cubes"></i> <span>Модули</span>', 'options' => [
                    'class' => 'sub-menu',
                    'data-pjax' => 0,
                ], 'url' => 'javascript:;', 'items' => $list],
                ['label' => '<i class="fa fa-user"></i> <span>Пользователи</span>', 'options' => [
                    'class' => 'sub-menu',
                    'data-pjax' => 0,
                ], 'url' => 'javascript:;', 'items' => [
                    ['label' => Yii::t('ra/view', 'Orders'), 'url' => ['order/index']],
                    ['label' => Yii::t('ra/view', 'Forms'), 'url' => ['form/index']],
                    ['label' => Yii::t('ra/view', 'Subscribes'), 'url' => ['subscribe/index']],
                    ['label' => Yii::t('ra/view', 'Orders'), 'url' => ['order/index']],
                ]],
                ['label' => '<i class="fa fa-cogs"></i> <span>Система</span>', 'options' => [
                    'class' => 'sub-menu',
                    'data-pjax' => 0,
                ], 'url' => 'javascript:;', 'items' => [
                    ['label' => Yii::t('ra/view', 'Modules'), 'url' => ['module/index']],
                    ['label' => Yii::t('ra/view', 'Characters'), 'url' => ['character/index']],
                    ['label' => Yii::t('ra/view', 'Settings'), 'url' => ['setting/index']],
                    ['label' => Yii::t('ra/view', 'Messages'), 'url' => ['message/index']],
                    ['label' => Yii::t('ra/view', 'Replaces'), 'url' => ['replace/index']],
                    ['label' => Yii::t('ra/view', 'File Manager'), 'url' => ['default/file-manager']],
                    ['label' => Yii::t('ra/view', 'Update'), 'url' => ['default/update']],
                ]],
            ],
        ]);
        ?>

        <? if (0): ?>
            <!-- sidebar menu start-->
            <ul class="sidebar-menu" id="nav-accordion">

                <p class="centered"><a href="profile.html"><img src="assets/img/ui-sam.jpg" class="img-circle"
                                                                width="60"></a></p>
                <h5 class="centered">Marcel Newman</h5>

                <li class="mt">
                    <a class="active" href="index.html">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sub-menu dcjq-parent-li">
                    <a href="javascript:;" class="dcjq-parent">
                        <i class="fa fa-desktop"></i>
                        <span>UI Elements</span>
                        <span class="dcjq-icon"></span></a>
                    <ul class="sub" style="display: none;">
                        <li><a href="general.html">General</a></li>
                        <li><a href="buttons.html">Buttons</a></li>
                        <li><a href="panels.html">Panels</a></li>
                    </ul>
                </li>

                <li class="sub-menu dcjq-parent-li">
                    <a href="javascript:;" class="dcjq-parent">
                        <i class="fa fa-cogs"></i>
                        <span>Components</span>
                        <span class="dcjq-icon"></span></a>
                    <ul class="sub" style="display: none;">
                        <li><a href="calendar.html">Calendar</a></li>
                        <li><a href="gallery.html">Gallery</a></li>
                        <li><a href="todo_list.html">Todo List</a></li>
                    </ul>
                </li>
                <li class="sub-menu dcjq-parent-li">
                    <a href="javascript:;" class="dcjq-parent">
                        <i class="fa fa-book"></i>
                        <span>Extra Pages</span>
                        <span class="dcjq-icon"></span></a>
                    <ul class="sub" style="display: none;">
                        <li><a href="blank.html">Blank Page</a></li>
                        <li><a href="login.html">Login</a></li>
                        <li><a href="lock_screen.html">Lock Screen</a></li>
                    </ul>
                </li>
                <li class="sub-menu dcjq-parent-li">
                    <a href="javascript:;" class="dcjq-parent">
                        <i class="fa fa-tasks"></i>
                        <span>Forms</span>
                        <span class="dcjq-icon"></span></a>
                    <ul class="sub" style="display: none;">
                        <li><a href="form_component.html">Form Components</a></li>
                    </ul>
                </li>
                <li class="sub-menu dcjq-parent-li">
                    <a href="javascript:;" class="dcjq-parent">
                        <i class="fa fa-th"></i>
                        <span>Data Tables</span>
                        <span class="dcjq-icon"></span></a>
                    <ul class="sub" style="display: none;">
                        <li><a href="basic_table.html">Basic Table</a></li>
                        <li><a href="responsive_table.html">Responsive Table</a></li>
                    </ul>
                </li>
                <li class="sub-menu dcjq-parent-li">
                    <a href="javascript:;" class="dcjq-parent">
                        <i class=" fa fa-bar-chart-o"></i>
                        <span>Charts</span>
                        <span class="dcjq-icon"></span></a>
                    <ul class="sub" style="display: none;">
                        <li><a href="morris.html">Morris</a></li>
                        <li><a href="chartjs.html">Chartjs</a></li>
                    </ul>
                </li>

            </ul>
            <!-- sidebar menu end-->
        <? endif ?>
    </div>
</aside>
