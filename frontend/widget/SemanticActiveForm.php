<?php

namespace common\widgets\semantic;

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\base\Widget;

class SemanticActiveForm extends Widget
{
    private $activeForm;
    private $baseTemplate;
    private $inputTemplate;
    private $inputWide;
    private $inputStyle;
    private $inputType;
    private $inputPlaceholder;

    public function init()
    {
        ob_start();
        $this->activeForm = ActiveForm::begin();

        if(!isset($this->params['inputWide']) || !empty($this->params['inputWide'])){
            $this->inputWide = 'sixteen';
        }
        if(!isset($this->params['inputStyle']) || !empty($this->params['inputStyle'])){
            $this->inputStyle = '';
        }
        if(!isset($this->params['inputType']) || !empty($this->params['inputType'])){
            $this->inputType = 'text';
        }
        if(!isset($this->params['inputTemplate']) || empty($this->params['inputTemplate'])){
            $this->inputTemplate = "<div class='ui $this->inputStyle fluid input'>
                                        {input}
                                    </div>";
        }

        $this->baseTemplate = "<div class='ui equal width stackable grid'>
                                    {hint}
                                    <div class='$this->inputWide column'>
                                        
                                    </div>
                                    {error}
                                </div>";
        parent::init();
    }

    public function field($model, $attribute)
    {
        echo new ActiveField([
            'form' => $this->activeForm,
            'model' => $model,
            'attribute' => $attribute,
            'template' => $this->baseTemplate
        ]);
    }

    public function run()
    {
        ActiveForm::end();
        ob_end_flush();
    }
}