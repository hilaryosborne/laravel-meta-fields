<?php

namespace Sackrin\Meta\Field;

class Hydrater {

    public $hydrated;

    public $blueprint;

    public $index;

    public function __construct() {
        // Where to store the hydrated fields
        $this->hydrated = collect([]);
        // Where to store the latest index
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
        $this->setHydrated($hydrated);
        // Rebuild the index after each field is added
        $this->reIndex();
        // Return for chaining
        return $this;
    }

    public function getHydrated() {
        // Return the found field
        return $this->hydrated;
    }

    public function setHydrated($fields) {
        // Update the current hydrated collection
        $this->hydrated = collect($fields);
        // Return for chaining
        return $this;
    }

    public function getIndex() {
        // Retrieve the index collection
        return $this->index;
    }

    public function reIndex() {
        // Reset the index collection
        $index = collect([]);
        // Loop through each of the hydrated fields
        $this->getHydrated()->each(function($field,$k) use ($index) {
            // Pass each field through the toPath process
            $field->toPath($index);
        });
        // Replace the current index value
        $this->index = $index;
        // Return for chaining
        return $this;
    }

    public function toObjects() {
        // Retrieve the current blueprint index
        // Since the index is stored as reference => object we don't need to do anything to it
        return $this->getIndex();
    }

    public function toClasses() {
        // Return a collection with the reference => classname pairing
        return $this->getIndex()->mapWithKeys(function(Field $blueprint, $key){
            // Determine the classname and the reference route
            return [$key => get_class($blueprint)];
        });
    }

    public function toOptions() {
        // Return a collection with the reference => options pairing
        return $this->getIndex()->mapWithKeys(function(Field $blueprint, $key){
            // Determine the options and the reference route
            return [$key => $blueprint->getOptions()];
        });
    }

    public function toValues() {
        // Create a new collection with the path and value
        return $this->getIndex()->mapWithKeys(function(Field $blueprint, $key){
            // Determine the value and the reference route
            return [$key => $blueprint->getValue()];
        });
    }

    public function toDefaults() {
        // Create a new collection with the path and default
        return $this->getIndex()->mapWithKeys(function(Field $field, $key){
            // Determine the default value and the reference route
            return [$key => $field->getDefault()];
        });
    }

}