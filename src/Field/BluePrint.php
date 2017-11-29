<?php

namespace Sackrin\Meta\Field;

class Blueprint {

    public $blueprints;

    public $index;

    public function __construct() {
        // The field objects
        $this->blueprints = collect([]);
        // Create a hydrated collection object
        $this->index = collect([]);
    }

    public function addBlueprint($field) {
        // Ensue this field is set as a blueprint
        $field->isBlueprint = true;
        $field->isHydrated = false;
        // Add to the fields collection
        $this->blueprints->push($field);
        // Rebuild the index after each field is added
        $this->reIndex();
        // Return for chaining
        return $this;
    }

    public function getBlueprint($path) {
        // Retrieve the field object index
        $fieldObjects = $this->toObjects();
        // If the field does not exist then return null
        if (!isset($fieldObjects[$path])) { return null; }
        // Retrieve the field object from the field indexer
        return $fieldObjects[$path];
    }

    public function reIndex() {
        // Reset the field collection
        $this->index = collect([]);
        // Loop through each of the sub fields
        foreach ($this->blueprints as $k => $blueprint) {
            // Convert the sub field to index
            $blueprint->toReference($this->index);
        }
        // Return for chaining
        return $this;
    }

    public function getIndex() {
        // Retrieve the index collection
        return $this->index;
    }

    public function toObjects() {
        // Retrieve the current field index
        return $this->getIndex();
    }

    public function toClasses() {
        // Retrieve the current field index
        $index = $this->getIndex();
        // Create a new collection with the path and classname
        return $index->mapWithKeys(function(Field $blueprint, $key){
            // Return the path with the classname
            return [$key => get_class($blueprint)];
        });
    }

    public function toOptions() {
        // Retrieve the current field index
        $index = $this->getIndex();
        // Create a new collection with the path and classname
        return $index->mapWithKeys(function(Field $blueprint, $key){
            // Return the path with the classname
            return [$key => $blueprint->getOptions()];
        });
    }

    public function toValues() {
        // Retrieve the current field index
        $index = $this->getIndex();
        // Create a new collection with the path and classname
        return $index->mapWithKeys(function(Field $blueprint, $key){
            // Return the path with the classname
            return [$key => $blueprint->getValue()];
        });
    }

    public function toDefaults() {
        // Retrieve the current field index
        $index = $this->getIndex();
        // Create a new collection with the path and classname
        return $index->mapWithKeys(function(Field $field, $key){
            // Return the path with the classname
            return [$key => $field->getDefault()];
        });
    }

}