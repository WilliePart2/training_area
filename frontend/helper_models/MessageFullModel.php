<?php

namespace frontend\helper_models;

use frontend\helper_models\MessageBaseModel;

class MessageFullModel extends MessageBaseModel
{
    public $messageId;
    public $messageDate;

    public function init($data)
    {
        parent::init($data);
        $this->messageId = $data['messageId'];
        $this->messageDate = $data['messageDate'];
    }
}