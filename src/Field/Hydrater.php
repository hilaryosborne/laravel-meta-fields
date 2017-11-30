<?php

namespace Sackrin\Meta\Field;

class Hydrater {

    public $hydrated;

    public $blueprint;

    public $index;

    public function __construct() {
        // The field objects
        $this->hydrated = collect([]);
        // Create a hydrated collection object
        $this->index = collect([]);
    }

    public function setBlueprint(Blueprint $blueprint) {
        // Set the blueprint object
        $this->blueprint = $blueprint;
        // Return for chaining
        return $this;
    }

    public function getBlueprint() {
        // Return the blueprint object
        return $this->blueprint;
    }

    public function hydrate($values) {
        // Retrieve the meta field blueprints
        $blueprints = $this->getBlueprint()->getBlueprints();
        // Create a collection to store the hydrated fields
        $hydrated = collect([]);
        // Loop through each of the blueprints and hydrate
        $blueprints->each(function(Field $blueprint, $k) use ($hydrated, $values) {
            // Retrieve the field machine code
            $machine = $blueprint->getMachine();
            // Retrieve the field value
            $value = isset($values[$machine]) ? $values[$machine] : null;
            // Retrieve the hydrated field instance
            $hydrated->push($blueprint->hydrate($value));
        });
        // Update the current hydrated collection
        $this->hydrated = $hydrated;
        // Rebuild the index after each field is added
        $this->reIndex();
        // Return for chaining
        return $this;
    }

    public function getHydrated($path) {
        // Retrieve the field object index
        $fieldObjects = $this->toObjects();
        // If the field does not exist then return null
        if (!isset($fieldObjects[$path])) { return null; }
        // Retrieve the field object from the field indexer
        return $fieldObjects[$path];
    }

    public function setHydrated($path) {
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
        foreach ($this->hydrated as $k => $blueprint) {
            // Convert the sub field to index
            $blueprint->toPath($this->index);
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