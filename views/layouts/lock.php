<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* template @url(http://www.blacktie.co/demo/dashgum/) */

use app\admin\widgets\adminTheme\AdminThemeAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\admin\assets\AppAsset;

AppAsset::register($this);

$this->registerJsFile(AdminThemeAsset::register($this)->baseUrl . '/js/jquery.backstretch.min.js');
$this->registerJs('$.backstretch("assets/img/login-bg.jpg", {speed: 500});');
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

<!-- **********************************************************************************************************************************************************
    MAIN CONTENT
    *********************************************************************************************************************************************************** -->

<div>
    <div class="container">

        <?= $content ?>

    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
