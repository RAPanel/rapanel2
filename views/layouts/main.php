<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* template @url(http://www.blacktie.co/demo/dashgum/) */

use ra\admin\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);

if (!in_array($this->context->module->id, ['rapanel']))
    die($content);

$iframe = Yii::$app->request->get('iframe');
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
<style>
    .header {
        z-index: 100;
    }

    .siteBack {
        position: relative;
        margin-left: -15px;
        float: left;
        width: 25px;
        height: 60px;
    }

    .siteBack a {
        top: 0;
        left: 0;
        border: solid transparent;
        height: 0;
        width: 0;
        position: absolute;
        border-top-color: white;
        border-left-color: white;
        border-width: 15px;
    }

    img {
        max-height: 100%;
    }

    .photoWrapper {
        padding: 0 10px;
    }

    .photoWrapper .image {
        float: left;
        display: block;
    }

    .photoWrapper [data-toggle="modal"] {
        display: block;
        padding: 0;
        margin: 5px;
        border: 0;
        height: 150px;
        max-width: 300px;
        cursor: pointer;
    }
</style>
<?php $this->beginBody() ?>

<?if(!$iframe):?>

<? require(__DIR__ . '/_header.php') ?>

<? require(__DIR__ . '/_aside.php') ?>

<?endif?>

<main id="<?= $iframe?'':'main-content' ?>">
    <section class="<?= $iframe?'':'wrapper' ?>">
        <div class="row">

            <?php // \yii\widgets\Pjax::begin(['timeout' => 3000, 'id' => 'pjax-admin-content']); ?>

            <? if(!$iframe) echo  Breadcrumbs::widget([
                'options' => [
                    'class' => 'breadcrumb',
                    'style' => 'margin-bottom: 0;',
                ],
                'homeLink' => [
                    'label' => Yii::t('ra', 'Home'),
                    'url' => '/rapanel',
                ],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>

            <div class="col-lg-12">

                <?= $content ?>

            </div>

            <?php // \yii\widgets\Pjax::end(); ?>

        </div>
    </section>
</main>

<footer class="footer hide">
    <div class="container">
        <p class="pull-left">&copy; ReRe-web <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
