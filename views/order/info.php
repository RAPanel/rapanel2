<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 12.10.2015
 * Time: 23:09
 */
use app\admin\models\Cart;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

?>



<?= DetailView::widget([
    'model' => $model,
    'attributes' => $model->serializeAttributes,
]) ?>

<?= GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getItems(), 'sort'=>false, 'pagination'=>false]),
    'layout'=>'{items}',
    'columns' => [
        'item_id',
        [
            'label'=>'Наименование',
            'format'=>'raw',
            'value' => function ($model) {
                return Html::a($model->data->name, $model->data->getHref(1,1), ['target'=>'_blank']);
            }
        ],
        'quantity',
        'price',
        [
            'label'=>'Сумма',
            'value' => function ($model) {
                return $model->price * $model->quantity;
            }
        ],
    ],
]); ?>
