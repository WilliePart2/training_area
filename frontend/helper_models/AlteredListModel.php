<?php

namespace frontend\helper_models;

use frontend\helper_models\ListItemModel;

class AlteredListModel
{
    public $postId;
    public $type;
    public $newItems;
    public $removingItems;
    public $alteredItems;
    public function __construct($data)
    {
        $props = get_class_vars(__CLASS__);
        foreach($props as $propName => $propValue) {
            if (!isset($data[$propName])) { throw new \Exception("Key '$propName' must exists in data model"); }

            if (is_array($data[$propName])) {
                $tmp = [];
                foreach ($data[$propName] as $value) {
                    $tmp[] = new ListItemModel($value);
                }
                $this->$propName = $tmp;
            }

            if (!is_array($data[$propName])) { $this->$propName = $data[$propName]; }
        }
    }
}