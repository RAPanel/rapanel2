<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rere.view', 'Messages');
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
$('.editable').click(function(){
    if($(':input', this).length) return;
    var el = $('div', this);
    var val = el.text();
    var data = el.data();
    var textarea = $('<textarea>').addClass('form-control').val(val).on('change live focusout', function(){
        data.translation = val = $(this).val();
        $.post('save', data);
        el.text(val);
    });
    $('div', this).html(textarea);
    textarea.focus();
    return false;
});
JS;
$this->registerJs($js);

?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rere.view', 'Create Message'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'category',
            'message:ntext',
            [
                'attribute' => 'messageTranslate.translation',
                'contentOptions' => ['style' => 'width:50%', 'class' => 'editable'],
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::tag('div', $model->messageTranslate->translation, ['data-id' => $model->id, 'data-language' => $model->messageTranslate->language, 'data-translation' => $model->messageTranslate->translation]);
                },
            ]
        ],
    ]); ?>

</div>
