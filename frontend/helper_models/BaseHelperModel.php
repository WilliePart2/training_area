<?php

namespace frontend\helper_models;


abstract class BaseHelperModel
{
    public function __construct($data)
    {
        $props = get_class_vars(__CLASS__);
        foreach($props as $prop) {
            if (!isset($data[$prop])) { throw new \Exception("Key '$prop' must exists in data model"); }
        }
        $this->init($data);
    }
    abstract public function init($data);
}