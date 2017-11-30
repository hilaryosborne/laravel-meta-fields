<?php

namespace Sackrin\Meta\Field\Type;

use Illuminate\Support\Collection;
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
        // Inject the cloned blueprint objects
        $instance->setBlueprints($this->getBlueprints());
        // Inject the cloned hydrated objects
        $instance->setHydrated($this->getHydrated());
        // Inject the cloned value
        $instance->value = $this->getValue();
        // Return the copied instance
        return $instance;
    }

    public function setBlueprints($blueprints) {
        // Set the new blueprint collection
        $this->blueprints = collect($blueprints);
        // Return for chaining
        return $this;
    }

    public function getBlueprints() {
        // Return the blueprint collection
        return $this->blueprints;
    }

    public function addBlueprint(Field $field) {
        // Set the field parent
        $field->setParent($this);
        // Add to the blueprint collection
        $this->getBlueprints()->push($field);
        // Return for chaining
        return $this;
    }

    public function childReference(Field $field) {
        // Return the reference with the child machine code
        return $this->getPath().'.'.$field->getMachine();
    }

    public function toReference(Collection $collection) {
        // Set the group field in the provided index collection
        $collection->put($this->getReference(), $this);
        // Retrieve the blueprints
        $blueprints = $this->getBlueprints();
        // Loop through each of the blueprints
        foreach ($blueprints as $k => $blueprint) {
            // Pass each blueprint through the to reference process
            $blueprint->toReference($collection);
        }
        // Return for chaining
        return $this;
    }

    public function setHydrated($hydrated) {
        // Set the hydrated collection
        $this->hydrated = collect($hydrated);
        // Return for chaining
        return $this;
    }

    public function getHydrated() {
        // Return the hydrated collection
        return $this->hydrated;
    }

    public function addHydrated(Field $field) {
        // Set the field parent
        $field->setParent($this);
        // Add to the hydrated collection
        $this->getHydrated()->push($field);
        // Return for chaining
        return $this;
    }

    public function childPath(Field $field) {
        // Return the path with the child machine code
        return $this->getPath().'.'.$field->getMachine();
    }

    public function toPath(Collection $collection) {
        // Set the group field in the provided index collection
        $collection->put($this->getPath(), $this);
        // Retrieve the fields which would be relevant
        $hydrated = $this->getHydrated();
        // Loop through each of the hydrated fields
        foreach ($hydrated as $k => $field) {
            // Pass the provided field object through the to path process
            $field->toPath($collection);
        }
        // Return for chaining
        return $this;
    }

    public function hydrate($values) {
        // Create a copy to hydrate
        $cloned = $this->cloneField();
        // Reset the hydrated fields to an empty collection
        $cloned->setHydrated(collect([]));
        // Inject the raw values to this field
        $cloned->value = $values;
        // Loop through each of the blueprints
        foreach ($this->getBlueprints() as $k => $blueprint) {
            // Retrieve the blueprint machine code
            $fieldMachine = $blueprint->getMachine();
            // Retrieve the provided field value value
            $fieldValue = isset($values[$fieldMachine]) ? $values[$fieldMachine] : null;
            // Hydrate the field and retrieve the hydrated instances
            $clonedField = $blueprint->hydrate($fieldValue);
            // Add the hydrated field into the hydrated group
            $cloned->addHydrated($clonedField);
        }
        // Return for chaining
        return $cloned;
    }

    public function setValue($values) {
        // Inject the raw values to this field
        $this->value = $values;
        // Reset the hydrated fields to an empty collection
        $this->setHydrated(collect([]));
        // Loop through each of the blueprints
        foreach ($this->getBlueprints() as $k => $blueprint) {
            // Retrieve the blueprint machine code
            $fieldMachine = $blueprint->getMachine();
            // Retrieve the provided field value value
            $fieldValue = isset($values[$fieldMachine]) ? $values[$fieldMachine] : null;
            // Hydrate the field and retrieve the hydrated instances
            $clonedField = $blueprint->hydrate($fieldValue);
            // Add the hydrated field into the hydrated group
            $this->addHydrated($clonedField);
        }
        // Return for chaining
        return $this;
    }

    public function getValue($formatted=true) {
        // Initially just return a raw value
        return $this->value;
    }

    public function serialize() {
        // Return a count of the number of hydrated field values
        return count($this->getHydrated());
    }

    public static function unserialize($value) {
        // Return null because the group does not need this information
        return null;
    }



}