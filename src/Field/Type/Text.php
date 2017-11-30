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

    public function getValue($formatted=true) {
        // Return the current value
        return $this->value;
    }

    public function serialize() {
        // Return the provided
        return $this->getValue(false);
    }

    public static function unserialize($serialized) {
        // Return the provided value
        return $serialized;
    }
}