<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 10.09.2015
 * Time: 17:25
 */
?>

<aside>
    <div id="sidebar" class="nav-collapse " tabindex="5000" style="overflow: hidden; outline: none;">

        <?
        $list = [];
        foreach (\app\admin\models\Module::find()->all() as $row)
            $list[] = ['label' => $row->name, 'url' => ['table/index', 'url' => $row->url]];

        echo \yii\widgets\Menu::widget([
            'activateParents' => true,
            'encodeLabels' => false,
            'submenuTemplate' => "\n<ul class='sub'>\n{items}\n</ul>\n",
            'options' => ['class' => 'sidebar-menu', 'id' => 'nav-accordion'],
            'items' => [
                ['label' => '<i class="fa fa-dashboard"></i> <span>Состояние</span>', 'url' => ['default/index']],
                ['label' => '<i class="fa fa-cubes"></i> <span>Модули</span>', 'options' => [
                    'class' => 'sub-menu',
                    'data-pjax'=>0,
                ], 'url' => 'javascript:;', 'items' => $list],
                ['label' => '<i class="fa fa-cogs"></i> <span>Система</span>', 'options' => [
                    'class' => 'sub-menu',
                    'data-pjax'=>0,
                ], 'url' => 'javascript:;', 'items' => [
                    ['label' => 'Модули', 'url' => ['module/index']],
                    ['label' => 'Характеристики', 'url' => ['character/index']],
                    ['label' => 'Параметры', 'url' => ['setting/index']],
                    ['label' => 'Сообщения', 'url' => ['message/index']],
                    ['label' => 'Замены', 'url' => ['replace/index']],
                    ['label' => 'Обновление', 'url' => ['default/update']],
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
