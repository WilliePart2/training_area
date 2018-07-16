<?php

namespace frontend\helper_models;

use frontend\helper_models\UserBaseModel;

class UserViewModel extends UserBaseModel
{
    public $avatar;
    public $username;
    public $type;
    public function init($data)
    {
        parent::init($data);
        $this->avatar = $data['avatar'];
        $this->username = $data['username'];
        $this->type = $data['type'];
    }
}