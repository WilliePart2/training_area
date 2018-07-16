<?php

namespace frontend\helper_models;

use frontend\helper_models\BasePostModel;

class VideoModel extends BasePostModel
{
    public $url;
    public function __construct($data)
    {
        $ownProps = get_class_vars(__CLASS__);
        foreach ($ownProps as $propName => $propValue) {
            if (empty($propName)) { continue; }
            if (!isset($data[$propName])) { throw new \Exception("Key '$prop' mus exists in data model"); }
            $this->$propName = $data[$propName];
        }
        parent::__construct($data);
    }
}