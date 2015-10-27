<?php

namespace ra\admin\models\arrays;

use ra\admin\models\Module;


/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 28.10.2015
 * Time: 11:30
 */
class Modules extends \yii\base\Model
{
    private $_data = false;
    private $_list = false;

    public function __get($name)
    {
        if (preg_match('#^id(\d+)&#', $name, $matches)) {
            $attr = 'id';
            $name = $matches[1];
        } else
            $attr = 'url';
        if (in_array($name, $this->getList($attr)))
            return $this->getData()[array_search($name, $this->getList($attr))];

        return parent::__get($name);
    }

    public function getList($attr = 'id')
    {
        if (empty($this->_list[$attr])) {
            $this->_list[$attr] = [];
            foreach ($this->getData() as $key => $row)
                $this->_list[$attr][$key] = $row->id;
        }
        return $this->_list[$attr];
    }

    public function getData()
    {
        if ($this->_data === false) {
            $this->_data = Module::find()->all();
        }
        return $this->_data;
    }

}