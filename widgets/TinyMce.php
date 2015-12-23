<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 14.04.2015
 * Time: 9:06
 */

namespace ra\admin\widgets;


use ra\admin\widgets\responsiveFilemanager\ResponsiveFilemanagerAsset;
use yii\web\JsExpression;

class TinyMce extends \dosamigos\tinymce\TinyMce
{
    public $language = 'ru';
    public $speller = true;
    public $fileManager = true;
    public $clientOptions = [
        'plugins' => [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern autoresize",
        ],
        'extended_valid_elements'=>'script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder]',
        'toolbar1' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | forecolor backcolor | fullscreen",
    ];

    public function init()
    {
        if($this->fileManager){
            $data = ResponsiveFilemanagerAsset::register($this->getView());
            $this->clientOptions['plugins'][] = 'responsivefilemanager';
            $this->clientOptions['external_filemanager_path'] = $data->baseUrl .'/';
            $this->clientOptions['image_advtab'] =  true;
            $this->clientOptions['filemanager_title'] =  "Управление файлами";
            $this->clientOptions['external_plugins']['filemanager'] = $data->baseUrl . '/tinymce/plugins/responsivefilemanager/plugin.min.js';
            $this->clientOptions['external_plugins']['responsivefilemanager'] = $data->baseUrl . '/tinymce/plugins/responsivefilemanager/plugin.min.js';
            $this->clientOptions['toolbar1'] = 'responsivefilemanager ' . $this->clientOptions['toolbar1'];
        }
        if($this->speller){
            $this->clientOptions['gecko_spellcheck'] = true;
            $this->clientOptions['browser_spellcheck'] = true;
            $this->clientOptions['plugins'][] = 'spellchecker';
            $this->clientOptions['toolbar1'] .= ' | spellchecker';
            $this->clientOptions['spellchecker_language'] = 'ru';
            $this->clientOptions['spellchecker_rpc_url'] = 'http://speller.yandex.net/services/tinyspell';
            $this->clientOptions['spellchecker_languages'] = 'Russian=ru,English=en,Ukrainian=uk';
        }

        parent::init();
    }
}