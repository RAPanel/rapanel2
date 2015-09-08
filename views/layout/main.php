<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\admin\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
NavBar::begin([
    'brandLabel' => 'RAPanel',
    'brandUrl' => ['default/index'],
    'options' => [
        'class' => 'navbar-default navbar-static-top',
    ],
]);

$list = [];
foreach (\app\admin\models\Module::find()->all() as $row)
    $list[] = ['label' => $row->name, 'url' => ['table/index', 'url' => $row->url]];

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => [
        ['label' => 'Модули', 'items' => $list],
        ['label' => 'Система', 'items' => [
            ['label' => 'Модули', 'url' => ['module/index']],
            ['label' => 'Характеристики', 'url' => ['character/index']],
            ['label' => 'Параметры', 'url' => ['setting/index']],
            ['label' => 'Сообщения', 'url' => ['message/index']],
            ['label' => 'Замены', 'url' => ['replace/index']],
        ]],
        Yii::$app->user->isGuest ?
            ['label' => 'Вход', 'url' => ['/site/login']] :
            [
                'label' => 'Выход (' . Yii::$app->user->identity->username . ')',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ],
    ],
]);
NavBar::end();
?>

<main>

    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => Yii::t('rere.view', 'Home'),
                'url' => '/rapanel',
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; ReRe-web <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
