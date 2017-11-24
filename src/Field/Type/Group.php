<?php

namespace Sackrin\Meta\Field\Type;

use Sackrin\Meta\Field\Field;

class Group extends Field {

    public static $defaults = [
        'machine' => '',
        'label' => '',
        'instructions' => ''
    ];

    public static $type = 'group';

    public $value = [];

    public $fields;

    public function __construct($machine) {
        // Call the parent constructor
        parent::__construct($machine);
        // The field objects
        $this->fields = collect([]);
    }

    public function addField($field) {
        // Set the field parent
        $field->setParent($this);
        // Add to the fields collection
        $this->fields->push($field);
        // Return for chaining
        return $this;
    }

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