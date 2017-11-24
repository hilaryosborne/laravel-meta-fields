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

    public $value = '';

    public function validate() {

        return true;
    }

    public function inject($data,$parent=false,$prefix=false) {

        $clone = $this->copy();

        $clone->setParent($parent);
    }

    public function values() {
        // Return the text value
        return $this->value;
    }
}