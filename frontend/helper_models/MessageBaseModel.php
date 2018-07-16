<?php

namespace frontend\helper_models;

use frontend\helper_models\BaseHelperModel;

class MessageBaseModel extends BaseHelperModel
{
    public $senderId;
    public $message;
    public $roomReceiverId;
    public function init($data)
    {
        $this->senderId = $data['senderId'];
        $this->message = $data['message'];
        $this->roomReceiverId = $data['roomReceiverId'];
    }
}