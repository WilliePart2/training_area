<?php

namespace frontend\helper_models;


class BasePostModel
{
    public $id;
    public $postId;
    public $type;
    public $header;
    public function __construct($data)
    {
        if (!isset($data['header'])) { throw new \Exception(" Key 'header' must exist in provided data"); }
        if (!isset($data['type'])) { throw new \Exception("Key 'type' must exist in provider data"); }
        if (!isset($data['id'])) { throw new \Exception("Key 'id' must exist in provided data"); }
        if (!isset($data['postId'])) { throw new \Exception("Key 'postId' must exist in data model"); }
        $this->id = $data['id'];
        $this->type = $data['type'];
        $this->header = $data['header'];
        $this->postId = $data['postId'];
    }
}