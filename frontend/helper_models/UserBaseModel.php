<?php

namespace frontend\helper_models;

use frontend\helper_models\BaseHelperModel;

class UserBaseModel extends BaseHelperModel
{
    public $id;
    public function init($data)
    {
        $this->id = $data['id'];
    }
}