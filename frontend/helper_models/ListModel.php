<?php


namespace frontend\helper_models;

use frontend\helper_models\BasePostModel;

class ListModel extends BasePostModel
{
    public $content;
    public function __construct($data)
    {
        $ownProps = get_class_vars(__CLASS__);
        foreach ($ownProps as $propName => $propValue) {
            if (empty($propName)) { continue; }

            if (!isset($data[$propName])) { throw  new \Exception("Key '$propName' must exists in data model"); }

            if (is_array($data[$propName])) {
                $tmp = [];
                foreach ($data[$propName] as $dataItem) {
                    $tmp[] = new ListItemModel($dataItem);
                }
                $this->content = $tmp;
            }
        }

        parent::__construct($data);
    }
}