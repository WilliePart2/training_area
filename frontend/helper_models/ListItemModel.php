<?php

namespace frontend\helper_models;


class ListItemModel
{
    public $id;
    public $value;
    public $vote;
    public function __construct(array $data)
    {
        if (!isset($data['id'])) { throw new \Exception("Key 'id' must exists in data model"); }
        if (!isset($data['value'])) { throw new \Exception("Ley 'value' must exists in data model"); }
        $this->id = $data['id'];
        $this->value = $data['value'];
        $this->vote = isset($data['vote']) && !empty($data['vote']) ? true : false;
    }
}