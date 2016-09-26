<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 21.03.2015
 * Time: 15:10
 */

namespace ra\admin\widgets;

use ra\admin\models\Order;
use yii;
use yii\web\View;

class Analytics extends \yii\base\Widget
{
    public $ya;
    public $ga;

    public function run()
    {
        $result = '';

        if (Yii::$app->session->get('lastOrderId')) {
            $this->addEvent('ORDER');
            $this->registerOrder(Yii::$app->session->get('lastOrderId'));
        }

        if (Yii::$app->session->get('updateCart'))
            $this->addEvent('ADD_CART');

        if ($this->ya) {
            Yii::$app->view->registerJs('(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter' . $this->ya . ' = new Ya.Metrika({id:' . $this->ya . ', webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true, trackHash:true,params:window.yaParams||{ }}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");', View::POS_END);
            $result .= '<noscript><div><img src="//mc.yandex.ru/watch/' . $this->ya . '" style="position:absolute; left:-9999px;" alt="" /></div></noscript>';
        }

        if ($this->ga) {
            Yii::$app->view->registerJs("(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{$this->ga}', 'auto');ga('send', 'pageview');", View::POS_END);
        }

        return $result;
    }

    public function registerOrder($id)
    {
        $this->addEvent('ORDER');
        if ($id && ($order = Order::findOne($id))) {
            $items = [];
            foreach ($order->items as $item)
                $items[] = [
                    'id' => $item->item_id,
                    'name' => $item->data->name,
                    'sku' => $item->item_id,
                    'category' => $item->data->parent->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ];

            if ($this->ya) Yii::$app->view->registerJs("var yaParams=" . yii\helpers\Json::encode([
                    'order_id' => $order->id,
                    'order_price' => $order->getTotal(),
                    'currency' => "RUR",
                    'exchange_rate' => 1,
                    'goods' => $items,
                ]) . ";", View::POS_END);

            if ($this->ga) {
                $itemString = '';
                foreach ($items as $item) {
                    $item['id'] = $order->id;
                    $itemString .= "window['ga']('ecommerce:addItem', " . yii\helpers\Json::encode($item) . ");";
                }

                Yii::$app->view->registerJs("window['ga']('require', 'ecommerce');window['ga']('ecommerce:addTransaction', " . yii\helpers\Json::encode([
                        'order_id' => $order->id,
                        'affiliation' => Yii::$app->name,
                        'revenue' => $order->getTotal(),
                        'shipping' => 0,
                        'tax' => 0,
                    ]) . ");{$itemString}window['ga']('ecommerce:send');", View::POS_READY);
            }
        }
        Yii::$app->session->set('lastOrderId', null);
    }

    public function addEvent($event)
    {
        if ($this->ya) Yii::$app->view->registerJs("window['yaCounter{$this->ya}'].reachGoal('{$event}');", View::POS_READY);
        if ($this->ga) Yii::$app->view->registerJs("window['ga']('send', 'event', 'siteLog', '{$event}');", View::POS_READY);
    }
}
