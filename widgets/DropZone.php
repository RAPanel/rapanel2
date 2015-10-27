<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 14.04.2015
 * Time: 9:38
 */

namespace ra\admin\widgets;


use ra\admin\assets\DropZoneAsset;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Request;
use yii\widgets\InputWidget;

class DropZone extends InputWidget
{
    /**
     * @var array An array of client events that are supported by Dropzone
     */
    public $clientEvents = [];
    //Default Values
    public $id = 'myDropzone';
    public $url = ['upload'];
    public $autoDiscover;

    /**
     * Initializes the widget
     * @throw InvalidConfigException
     */
    public function init()
    {
        parent::init();
        //set defaults
        if (!isset($this->options['url'])) $this->options['url'] = Url::to($this->url);
        $this->autoDiscover = !$this->autoDiscover ? 'false' : 'true';

        if (Yii::$app->getRequest()->enableCsrfValidation)
            $this->options['headers'][Request::CSRF_HEADER] = \Yii::$app->getRequest()->getCsrfToken();

        $this->registerAssets();
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        $js = 'Dropzone.autoDiscover = ' . $this->autoDiscover . '; var ' . $this->id . ' = new Dropzone("div#' . $this->id . '", ' . Json::encode($this->options) . ');';
        if (!empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                $js .= "$this->id.on('$event', $handler);";
            }
        }
        $view->registerJs($js);
        DropZoneAsset::register($view);
    }

    public function run()
    {
        $result = '';
        $result .= Html::beginTag('div', ['class' => 'dropzone', 'id' => $this->id]);
        $result .= Html::tag('div', Html::activeFileInput($this->model, $this->attribute, ['class' => 'fileInput', 'value' => false]), ['class' => 'fallback']);
        $result .= Html::endTag('div');

        return $result;
    }
}