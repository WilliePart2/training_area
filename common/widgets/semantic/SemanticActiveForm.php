<?php

namespace common\widgets\semantic;

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\base\Widget;

class SemanticActiveForm extends Widget
{
    public $activeForm;
    public $baseTemplate;

    public $inputTemplate;
    public $inputWide;
    public $inputStyle;
    public $inputType;
    public $inputPlaceholder;
    public $inputIcon;

    public $labelStyle;
    public $labelWide;

    public $errorOptions;
    public $hintOptions;
    public $inputOptions;
    public $labelOptions;
    public $options;
    public $selectors;

    public function init()
    {
        ob_start();
        $this->activeForm = ActiveForm::begin([
            'options' => [
                'class' => 'ui form'
            ]
        ]);

        $this->_ifNotSet($this->labelStyle);
        $this->_ifNotSet($this->labelWide, 'four');

        $this->_ifNotSet($this->inputWide, 'sixteen');
        $this->_ifNotSet($this->inputStyle);
        $this->_ifnotSet($this->inputType, 'text');
        $this->_ifNotSet($this->inputPlaceholder);
        $this->_ifNotSet($this->inputIcon);
        if(!isset($this->inputTemplate) || empty($this->inputTemplate)){
            $this->inputTemplate = "<div class='field'>
                                        <div class='ui $this->labelStyle $this->labelWide header'>
                                                {label}
                                        </div>
                                        <div class='ui $this->inputStyle fluid input'>
                                            {input}
                                        </div>
                                    </div>
                                    ";
        }

        $this->baseTemplate = "<div class='ui equal width stackable centered grid'>
                                    <div class='$this->inputWide column'>
                                        {hint}
                                        $this->inputTemplate
                                        {error}
                                    </div>
                                </div>";

        $this->_ifNotSet($this->labelOptions, ['class' => '']);
        $this->_ifNotSet($this->inputOptions, ['class' => '']);
        $this->_ifNotSet($this->errorOptions, [
            'encode' => false,
            'class' => 'error-message'
        ]);
        $this->_ifNotSet($this->hintOptions, ['class' => '']);
        $this->_ifNotSet($this->options, ['class' => null]);

        parent::init();
    }

    public function field($model, $attribute, array $options = [])
    {
        $config = [
            'form' => $this->activeForm,
            'model' => $model,
            'attribute' => $attribute,
            'template' => $this->baseTemplate,
            'inputOptions' => $options,
            'labelOptions' => $this->labelOptions,
            'errorOptions' => $this->errorOptions,
            'hintOptions' => $this->hintOptions,
            'options' => $this->options
        ];

//        $config['selectors'] = isset($this->selectors) && !empty($this->selector) ? $this->selectors : [];
        $this->_ifNotSet($config['inputOptions'], $this->inputOptions);
        $this->_ifNotSet($config['inputOptions']['type'], $this->inputType);
        $this->_ifNotSet($config['inputOptions']['placeholder'], $this->inputPlaceholder);

        return new ActiveField($config);
    }
    public function passwordInput($model, $attribute, array $config = [])
    {
        $config['type'] = 'password';
        return $this->field($model, $attribute, $config);
    }
    public function emailInput($model, $attribute, array $config =[])
    {
        $config['type'] = 'email';
        return $this->field($model, $attribute, $config);
    }
    public function fileInput($model, $attribute, array $config = [])
    {
        $config['type'] = 'file';
        return $this->field($model, $attribute, $config);
    }
    public function hiddenInput($model, $attribute, array $config = [])
    {
        $config['type'] = 'hidden';
        return $this->field($model, $attribute, $config);
    }

    public function run()
    {
        ActiveForm::end();
        ob_end_flush();
    }

    private function _ifNotSet(&$attr, $defaultValue = null)
    {
        if(empty($defaultValue)) $defaultValue = '';
        if(!isset($attr) || empty($attr)){
            $attr = $defaultValue;
        }
    }
}