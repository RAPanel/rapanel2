<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 07.10.2015
 * Time: 16:52
 */

namespace ra\admin\traits;


trait SerializeAttribute
{
    private $_data = [];

    public function __get($name)
    {
        if (in_array($name, $this->serializeAttributes)) {
            if (empty($this->_data)) $this->_data = unserialize($this->data);
            return isset($this->_data[$name]) ? $this->_data[$name] : null;
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->serializeAttributes)) {
            if ($this->_data === false) $this->_data = unserialize($this->_data);
            $this->_data[$name] = $value;
            $this->on($this->isNewRecord ? parent::EVENT_BEFORE_INSERT : parent::EVENT_BEFORE_UPDATE, function ($event) {
                if ($event->sender->_data) {
                    $event->sender->data = serialize($event->sender->_data);
                    $event->sender->_data = false;
                }
            });
            return;
        }
        parent::__set($name, $value);
    }

}