<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 05.09.2015
 * Time: 11:45
 */

namespace ra\admin\widgets;


use nex\chosen\Chosen;
use nex\chosen\ChosenAsset;
use yii\helpers\Json;

class ChosenSortable extends Chosen
{
    /**
     * Registers chosen.js
     */
    public function registerScript()
    {
        ChosenAsset::register($this->getView());
        ChosenSortableAsset::register($this->getView());
        $clientOptions = Json::encode($this->clientOptions);
        $id = $this->options['id'];
        $this->getView()->registerJs("chosenSortable('#$id', {$clientOptions});");
    }
}