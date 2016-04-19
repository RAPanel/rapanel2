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
        if (isset($this->{$name})) {
            return isset($this->_data[$name]) ? $this->_data[$name] : null;
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (isset($this->{$name})) {
            $this->_data[$name] = $value;
            $this->on($this->isNewRecord ? parent::EVENT_BEFORE_INSERT : parent::EVENT_BEFORE_UPDATE, function ($event) {
                if (count($event->sender->_data)) {
                    $event->sender->data = serialize($event->sender->_data);
                    $event->sender->_data = [];
                }
            });
            return;
        }
        parent::__set($name, $value);
    }

    public function __isset($name)
    {
        if($name != 'data'){
            if (empty($this->_data) && $this->data) $this->_data = unserialize($this->data);
            if (in_array($name, $this->serializeAttributes) || isset($this->_data[$name])) return true;
        }
        return parent::__isset($name);
    }

}