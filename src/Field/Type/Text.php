<?php

namespace Sackrin\Meta\Field\Type;

use Sackrin\Meta\Field\Field;

class Text extends Field {

    public static $defaults = [
        'machine' => '',
        'label' => '',
        'instructions' => '',
        'required' => false,
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'maxlength' => ''
    ];

    public static $type = 'text';

    public $value;

    public function getValue($formatted=true) {
        // Initially just return a raw value
        return $this->value;
    }
}