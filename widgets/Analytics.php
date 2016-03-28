<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 21.03.2015
 * Time: 15:10
 */

namespace ra\admin\widgets;

use yii;
use yii\web\View;

class Analytics extends \yii\base\Widget
{
    public $ya;
    public $ga;

    public function run()
    {
        $result = '';

        if (!empty($this->ya)) {
            Yii::$app->view->registerJs('(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter' . $this->ya . ' = new Ya.Metrika({id:' . $this->ya . ', webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true, trackHash:true,params:window.yaParams||{ }}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks")var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");', View::POS_END);
            $result .= '<noscript><div><img src="//mc.yandex.ru/watch/' . $this->ya . '" style="position:absolute; left:-9999px;" alt="" /></div></noscript>';
        }

        if (!empty($this->ga)) {
            Yii::$app->view->registerJs("(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{$this->ga}', 'auto');ga('send', 'pageview');", View::POS_END);
        }

        return $result;
    }
}
