<?php

namespace Sackrin\Meta\Field;

use Sackrin\Meta\Field\Type\Group;
use Underscore\Types\Arrays;

class Schema {

    public $fieldsIndex;

    public $fields = [];

    public function __construct() {
        // The field objects
        $this->fields = collect([]);
    }

    public function addField($field) {
        // Add to the fields collection
        $this->fields->push($field);
        // Return for chaining
        return $this;
    }

    public function buildFieldIndex() {
        // Create a new collection
        $this->fieldsIndex = collect([]);
        // Populate the field index with the result of full tree indexing
        $this->toIndex($this->fieldsIndex);
        // Return for chaining
        return $this;
    }

    public function getFieldIndex() {
        // If we currently have no field index
        if (!$this->fieldsIndex) {
            // Build the current index
            $this->buildFieldIndex();
        }
        // Return the index collection
        return $this->fieldsIndex;
    }

    public function getFieldTypeIndex() {
        // Retrieve the current field index
        $fields = $this->getFieldIndex();
        // Create a new collection with the path and classname
        return $fields->mapWithKeys(function($field, $key){
            // Return the path with the classname
            return [$key => get_class($field)];
        });
    }

    public function getField($path) {
        // Retrieve the current field index
        $fields = $this->getFieldIndex();
        // Retrieve the name values
        return isset($fields[$path]) ? $fields[$path] : false;
    }

}