<?php

namespace Sackrin\Meta\Field;

class Manager {

    public $model;

    public $schema;

    public $values;

    public $fields;

    public $fieldsIndex;

    public function __construct($model) {
        // Update the model
        $this->model = $model;
        // Retrieve the field schema
        $this->schema = $model::getSchema();
        // The field objects
        $this->fields = collect([]);
    }

    public function hydrate($values) {
        // The collection of hydrated fields
        $hydrated = collect([]);
        // Retrieve the schema fields
        $fields = $this->schema->fields;
        // Loop through each of the fields
        foreach ($fields as $k => $field) {
            // Retrieve the field machine code
            $fieldMachine = $field->getMachine();
            // Retrieve the field value
            $fieldValue = isset($values[$fieldMachine]) ? $values[$fieldMachine] : null;
            // Hydrate the field
            $hydratedField = $field->hydrate($fieldValue);
            // Add the field into the hydrated group
            $hydrated->push($hydratedField);
        }
        // Return the hydrated collection
        $this->fields = $hydrated;
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

    public function toIndex($collection) {
        // Loop through each of the sub fields
        foreach ($this->fields as $k => $field) {
            // Convert the sub field to index
            $field->toIndex($collection);
        }
    }

    public function setValues() {
        // Return for chaining
        return $this;
    }

    /**
     * Get Meta Field
     * @param $path
     * @param string $format
     * @return bool
     */
    public function getField($path,$format='value') {
        // Retrieve the current field index
        $fieldIndex = $this->getFieldIndex();
        // If the field does not exist then return null
        if (!isset($fieldIndex[$path])) { return null; }
        // Retrieve the field object from the field index
        $field = $fieldIndex[$path];
        // Return the required field value
        switch(strtolower($format)) {
            case 'value' : return $field->getValue(true); break;
            case 'raw' : return $field->getValue(false); break;
            case 'object' : return $field; break;
            default : $field->getValue(true); break;
        }
    }

    /**
     * Set Meta Field
     * @param $path
     * @param $value
     * @param string $format
     * @return bool
     */
    public function setField($path,$value,$format='value') {
        // Retrieve the current field index
        $fields = $this->getFieldIndex();
        // Retrieve the name values
        return isset($fields[$path]) ? $fields[$path] : false;
    }

}