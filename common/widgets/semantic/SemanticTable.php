<?php

namespace common\widgets\semantic;

use yii\base\Widget;

class SemanticTable extends Widget
{
    public $tableClass;

    public function init()
    {
        $this->ifNotSet($this->tableClass, $this->arrayToString([
            'ui',
            'unstackable',
            'very compact',
            'celled',
            'table'
        ]));

        parent::init();
        ob_start();
        echo "<table class='$this->tableClass'>";
    }
    public function row($cellType, array $cells)
    {
        return $this->arrayToString($cells, $cellType);
    }
    public function run()
    {
        echo "</table>";
        ob_end_flush();
    }

    /**
     * Вспомогательне методы
    */
    private function ifNotSet(&$item, $defaultValue)
    {
        if(!isset($item) || empty($item)){
            $item = $defaultValue;
        }
    }
    private function arrayToString(array $args, $tag = null)
    {
        if(!is_array($args)) throw new \Exception('Аргумент должен быть масивом');
        if(empty($tag)) {
            $tagStart = ' ';
            $tagClose = '';
        }
        else {
            $tagStart = $this->startTag($tag);
            $tagClose = $this->closeTag($tag);
        }

        $str = '';
        foreach($args as $val){
            $str .= $tagStart . $val . $tagClose;
        }
        return $str;
    }
    private function tagConfigToHtml(array $tagConfig)
    {
        $str = $tagConfig['tag'];
        foreach($tagConfig as $attr => $value){
            if(strtolower($attr) === 'tag') continue;

            if(is_array($value)) $str .= ' ' . $this->arrayToString($value);

            $str .= ' ' . $attr . '="' . $value . '"';
        }
        return $str;
    }
    private function startTag($tag)
    {
        if(is_array($tag)) return "<" . $this->tagConfigToHtml($tag) . ">";
        return "<$tag>";
    }
    private function closeTag($tag)
    {
        if(is_array($tag)) return "</" . $tag['tag'] . ">";
        return "</$tag>";
    }
}