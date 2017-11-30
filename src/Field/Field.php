<?php

namespace Sackrin\Meta\Field;

use Illuminate\Support\Collection;

abstract class Field {

    public $parentField;

    public $options = [];

    public $value;

    public $position;

    public static $defaults = [
        'machine' => '',
        'label' => '',
        'instructions' => '',
        'required' => false,
        'default_value' => ''
    ];

    public function __construct($machine) {
        // Populate with the defaults for the field
        $this->setOptions(static::$defaults);
        // Set the field machine code
        $this->setMachine($machine);
    }

    public function setParent(Field $parentField) {
        // Save the parentField for future use
        $this->parentField = $parentField;
        // Return for chaining
        return $this;
    }

    public function getParent() {
        // Return the parent field object
        return $this->parentField;
    }

    public function setOptions($options, $merge=true) {
        // Replace or merge the new options with the existing options
        $this->options = $merge ? array_merge($this->options, $options) : $options;
        // Return for chaining
        return $this;
    }

    public function getOptions() {
        // Retrieve the field options
        return $this->options;
    }

    public function setMachine($machine) {
        // Set the field machine code
        $this->options['machine'] = $machine;
        // Return for chaining
        return $this;
    }

    public function getMachine() {
        // Return the field machine code
        return $this->options['machine'];
    }

    public function setInstructions($instructions) {
        // Set the instruction options value
        $this->options['instructions'] = $instructions;
        // Return for chaining
        return $this;
    }

    public function getInstructions() {
        // Return the instructions value
        return $this->options['instructions'];
    }

    public function setRequired($required) {
        // Set the required option value
        $this->options['required'] = (bool)$required;
        // Return for chaining
        return $this;
    }

    public function getRequired() {
        // Return the required value
        return (bool)$this->options['required'];
    }

    public function setDefault($default) {
        // Set the field's default value
        $this->options['default_value'] = $default;
        // Return for chaining
        return $this;
    }

    public function getDefault() {
        // Return the field's default value
        return $this->options['default_value'];
    }


    public function setPosition($position) {
        // Set the field's current child position
        $this->position = (int)$position;
        // Return for chaining
        return $this;
    }

    public function getPosition() {
        // Return the field's position
        return (int)$this->position;
    }


    public function cloneField() {
        // Create a new instance of this field object
        $instance = new static($this->getMachine());
        // Inject the cloned options
        $instance->setOptions($this->getOptions());
        // Inject the cloned values
        $instance->setValue($this->getValue());
        // Return the copied instance
        return $instance;
    }


    public function getReference() {
        // If this field have a parent field
        if ($this->getParent()) {
            // Allow the parent to determine the child's reference
            return $this->getParent()->childReference($this);
        } // Otherwise return just the machine key
        else { return $this->getMachine(); }
    }

    public function toReference(Collection $collection) {
        // Add this field's reference route to the provided index collection
        $collection->put($this->getReference(), $this);
        // Return for chaining
        return $this;
    }


    public function getPath() {
        // If this field have a parent field
        if ($this->getParent()) {
            // Allow the parent to determine the child's path
            return $this->getParent()->childPath($this);
        } // Otherwise return just the machine key
        else { return $this->getMachine(); }
    }

    public function toPath(Collection $collection) {
        // Add this field's path route to the provided index collection
        $collection->put($this->getPath(), $this);
        // Return for chaining
        return $this;
    }

    public function hydrate($value) {
        // Create a copy to hydrate
        $instance = $this->cloneField();
        // Hydrate the field
        $instance->value = $value;
        // Return the text value
        return $instance;
    }

    public function setValue($value) {
        // Set the field value
        $this->value = $value;
        // Return for chaining
        return $this;
    }

    abstract public function getValue($formatted=true);

    abstract public function serialize();

    abstract public static function unserialize($serialized);

}