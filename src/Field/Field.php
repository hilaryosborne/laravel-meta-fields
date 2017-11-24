<?php

namespace Sackrin\Meta\Field;

abstract class Field {

    public $parent;

    public $options = [];

    public $index = null;

    public $breadcrumb = '';

    public static $defaults = [
        'machine' => '',
        'label' => '',
        'instructions' => '',
        'required' => false,
        'default_value' => ''
    ];

    public function __construct($machine) {
        // Populate with the defaults for the field
        $this->options = static::$defaults;
        // Update the field options values
        $this->options['machine'] = $machine;
        // Update the field options values
        $this->breadcrumb = $this->getBreadcrumb();
    }

    public function setParent($parent) {
        // Save the parent for future use
        $this->parent = $parent;
        // Update the field options values
        $this->breadcrumb = $this->getBreadcrumb();
        // Return for chaining
        return $this;
    }

    public function getBreadcrumb() {
        // Determine the machine code
        $machine = $this->index !== null ? $this->options['machine'].'.'.$this->options['machine'] : $this->options['machine'];
        // If a parent object was returned
        if ($this->parent) {
            // Merge with the parent's code
            return $this->parent->getBreadcrumb().'.'.$machine;
        } // Otherwise return the standard key
        else { return $machine; }
    }

    public function setOptions($options) {
        // Update the options with the options
        $this->options = array_merge($this->options, $options);
        // Return for chaining
        return $this;
    }

    public function setInstructions($instructions) {
        // Set the setting option value
        $this->options['instructions'] = $instructions;
        // Return for chaining
        return $this;
    }

    public function setRequired($required) {
        // Set the setting option value
        $this->options['required'] = $required;
        // Return for chaining
        return $this;
    }

    public function setDefault($default) {
        // Set the setting option value
        $this->options['default_value'] = $default;
        // Return for chaining
        return $this;
    }

    public function tooptions() {
        // Return the field's options
        return $this->options;
    }

    public function copy() {
        // Create a new instance of this field object
        $instance = new static($this->options['machine']);
        // Inject the new options
        $instance->setOptions($this->options);
        // Return the copied instance
        return $instance;
    }

    abstract public function validate();

    abstract public function inject($data,$prefix=false);

    abstract public function values();

}