<?php

namespace frontend\helper_models;

use frontend\helper_models\BasePostModel;

class ArticleModel extends BasePostModel
{
    public $content;
    public function __construct($data)
    {
        parent::__construct($data);
        if (!isset($data['content'])) { throw new \Exception("Key 'content' must exist in data model"); }
        $this->content = $data['content'];
    }
}