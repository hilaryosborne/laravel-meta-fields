<?php

namespace Sackrin\Meta\Field\Templater;

use Sackrin\Meta\Field\Field;

class Indexer {

    public $fieldCollection;

    public $fieldIndex;

    public function __construct(Collection $fieldCollection) {
        // Populate the field collection
        $this->fieldCollection = $fieldCollection;
        // Initialise the index with a laravel collection
        $this->fieldIndex = collect([]);
    }

    public function getCollection() {
        // Return the field collection
        return $this->fieldCollection;
    }

    public function build() {
        // Reset the field collection
        $this->fieldIndex = collect([]);
        // Retrieve the collection's fields
        $fields = $this->getCollection()->getFields();
        // Loop through each of the sub fields
        foreach ($fields as $k => $field) {
            // Convert the sub field to index
            $field->toIndex($this->fieldIndex);
        }
        // Return for chaining
        return $this;
    }

    public function getFieldIndex() {
        // Return the index collection
        return $this->fieldIndex;
    }

    public function toObjects() {
        // Retrieve the current field index
        return $this->getFieldIndex();
    }

    public function toClasses() {
        // Retrieve the current field index
        $index = $this->getFieldIndex();
        // Create a new collection with the path and classname
        return $index->mapWithKeys(function(Field $field, $key){
            // Return the path with the classname
            return [$key => get_class($field)];
        });
    }

    public function toOptions() {
        // Retrieve the current field index
        $index = $this->getFieldIndex();
        // Create a new collection with the path and classname
        return $index->mapWithKeys(function(Field $field, $key){
            // Return the path with the classname
            return [$key => $field->getOptions()];
        });
    }

    public function toValues() {
        // Retrieve the current field index
        $index = $this->getFieldIndex();
        // Create a new collection with the path and classname
        return $index->mapWithKeys(function(Field $field, $key){
            // Return the path with the classname
            return [$key => $field->getValue()];
        });
    }

}