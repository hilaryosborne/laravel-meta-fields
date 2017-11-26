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

    public $fields = [];

    public $templates = [];

    public function __construct($machine) {
        // Call the parent constructor
        parent::__construct($machine);
        // The field objects
        $this->fields = collect([]);
        // The field objects
        $this->templates = collect([]);
    }

    public function getChildPath($field) {

        return $this->getPath().'.'.$field->getMachine();
    }

    public function setFields($fields) {
        // Set the new template collection
        $this->fields = collect($fields);
        // Return for chaining
        return $this;
    }

    public function getFields() {
        // Return the template collection
        return $this->fields;
    }

    public function setTemplates($templates) {
        // Set the new template collection
        $this->templates = collect($templates);
        // Return for chaining
        return $this;
    }

    public function getTemplates() {
        // Return the template collection
        return $this->templates;
    }

    public function cloneField() {
        // Create a new instance of this field object
        $instance = new static($this->getMachine());
        // Inject the cloned options
        $instance->setOptions($this->options);
        // Inject the cloned field objects
        $instance->setFields($this->getFields());
        // Inject the cloned template objects
        $instance->setTemplates($this->getTemplates());
        // Inject the cloned values
        $instance->setValue($this->getValue());
        // Inject the cloned hydration state
        $instance->setHydrated($this->getHydrated());
        // Return the copied instance
        return $instance;
    }

    public function addField($field) {
        // Set the field parent
        $field->setParentField($this);
        // Add to the fields collection
        $this->getFields()->push($field);
        // Return for chaining
        return $this;
    }

    public function addTemplate($field) {
        // Set the field parent
        $field->setParentField($this);
        // Add to the fields collection
        $this->getTemplates()->push($field);
        // Return for chaining
        return $this;
    }

    public function toIndex($collection) {
        // Set the field in the index collection
        $collection->put($this->getPath(), $this);
        // Retrieve the fields which would be relevant
        $fields = $this->getHydrated() ? $this->getFields() : $this->getTemplates() ;
        // Loop through each of the sub fields
        foreach ($fields as $k => $field) {
            // Convert the sub field to index
            $field->toIndex($collection);
        }
    }

    public function getHydratedField($values) {
        // Create a copy to hydrate
        $cloned = $this->cloneField();
        // Reset the fields to an empty collection
        $cloned->setFields(collect([]));
        // Inject the raw new values
        $cloned->setValue($values);
        // Loop through each of the fields
        foreach ($this->getTemplates() as $k => $field) {
            // Retrieve the field machine code
            $fieldMachine = $field->getMachine();
            // Retrieve the field value
            $fieldValue = isset($values[$fieldMachine]) ? $values[$fieldMachine] : null;
            // Hydrate the field
            $clonedField = $field->getHydratedField($fieldValue);
            // Add the field into the hydrated group
            $cloned->addField($clonedField);
        }
        // Set that this is a hydrated field
        $cloned->setHydrated(true);
        // Return the text value
        return $cloned;
    }

    public static function serialize($value) {

        return count($value);
    }

    public static function unserialize($value) {

        return null;
    }

    public function getValue($formatted=true) {
        // Initially just return a raw value
        return $this->value;
    }

}