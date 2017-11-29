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

    public $hydrated;

    public $blueprints;

    public function __construct($machine) {
        // Call the parent constructor
        parent::__construct($machine);
        // The field objects
        $this->hydrated = collect([]);
        // The field objects
        $this->blueprints = collect([]);
    }



    public function cloneField() {
        // Create a new instance of this field object
        $instance = new static($this->getMachine());
        // Inject the cloned options
        $instance->setOptions($this->options);
        // Inject the cloned field objects
        $instance->setHydrated($this->getHydrated());
        // Inject the cloned template objects
        $instance->setBlueprints($this->getBlueprints());
        // Inject the cloned values
        $instance->setValue($this->getValue());
        // Ensue this field is set as a blueprint
        $instance->isBlueprint = $this->isBlueprint;
        $instance->isHydrated = $this->isHydrated;
        // Return the copied instance
        return $instance;
    }



    public function setBlueprints($blueprints) {
        // Set the new template collection
        $this->blueprints = collect($blueprints);
        // Return for chaining
        return $this;
    }

    public function getBlueprints() {
        // Return the template collection
        return $this->blueprints;
    }

    public function addBlueprint(Field $field) {
        // Set the field parent
        $field->setParentField($this);
        // Add to the fields collection
        $this->getBlueprints()->push($field);
        // Return for chaining
        return $this;
    }

    public function childReference(Field $field) {
        // Return the path with a placeholder for the repeater child
        return $this->getPath().'.'.$field->getMachine();
    }

    public function toReference($collection) {
        // Set the field in the index collection
        $collection->put($this->getReference(), $this);
        // Retrieve the fields which would be relevant
        $fields = $this->getBlueprints();
        // Loop through each of the sub fields
        foreach ($fields as $k => $field) {
            // Convert the sub field to index
            $field->toReference($collection);
        }
    }



    public function setHydrated($hydrated) {
        // Return the template collection
        $this->hydrated = $hydrated;
        // Return for chaining
        return $this;
    }

    public function getHydrated() {
        // Return the template collection
        return $this->hydrated;
    }

    public function addHydrated(Field $field) {
        // Set the field parent
        $field->setParentField($this);
        // Add to the fields collection
        $this->getHydrated()->push($field);
        // Return for chaining
        return $this;
    }

    public function childPath(Field $field) {
        // Return the path with a placeholder for the repeater child
        return $this->getPath().'.'.$field->getMachine();
    }

    public function toPath($collection) {
        // Set the field in the index collection
        $collection->put($this->getPath(), $this);
        // Retrieve the fields which would be relevant
        $fields = $this->getHydrated();
        // Loop through each of the sub fields
        foreach ($fields as $k => $field) {
            // Convert the sub field to index
            $field->toPath($collection);
        }
    }

    public function hydrate($values) {
        // Create a copy to hydrate
        $cloned = $this->cloneField();
        // Reset the fields to an empty collection
        $cloned->setHydrated(collect([]));
        // Inject the raw new values
        $cloned->value = $values;
        // Loop through each of the fields
        foreach ($this->getBlueprints() as $k => $field) {
            // Retrieve the field machine code
            $fieldMachine = $field->getMachine();
            // Retrieve the field value
            $fieldValue = isset($values[$fieldMachine]) ? $values[$fieldMachine] : null;
            // Hydrate the field
            $clonedField = $field->hydrate($fieldValue);
            // Add the field into the hydrated group
            $cloned->addHydrated($clonedField);
        }
        // Return the text value
        return $cloned;
    }



    public function setValue($values) {
        // Inject the raw new values
        $this->value = $values;
        // Reset the fields to an empty collection
        $this->setHydrated(collect([]));
        // Loop through each of the fields
        foreach ($this->getBlueprints() as $k => $field) {
            // Retrieve the field machine code
            $fieldMachine = $field->getMachine();
            // Retrieve the field value
            $fieldValue = isset($values[$fieldMachine]) ? $values[$fieldMachine] : null;
            // Hydrate the field
            $clonedField = $field->hydrate($fieldValue);
            // Add the field into the hydrated group
            $this->addHydrated($clonedField);
        }
        // Return for chaining
        return $this;
    }

    public function getValue($formatted=true) {
        // Initially just return a raw value
        return $this->value;
    }

    public static function serialize($value) {

        return count($value);
    }

    public static function unserialize($value) {

        return null;
    }



}