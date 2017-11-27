<?php

namespace Sackrin\Meta\Field\Hydrater;

use Sackrin\Meta\Field\FieldCollection;

class Collection implements FieldCollection {

    public $indexer;

    public $fields = [];

    public $templates;

    public function __construct($templates) {
        // The field objects
        $this->fields = collect([]);
        // Create a new indexerer
        $this->indexer = new Indexer($this);
        // Save the templates
        $this->templates = $templates;
    }

    public function addField($field) {
        // Add to the fields collection
        $this->fields->push($field);
        // Return for chaining
        return $this;
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

    public function hydrateFields($values) {
        // The collection of hydrated fields
        $hydrated = collect([]);
        // Retrieve the schema fields
        $fields = $this->templates->getFields();
        // Loop through each of the fields
        foreach ($fields as $k => $field) {
            // Retrieve the field machine code
            $fieldMachine = $field->getMachine();
            // Retrieve the field value
            $fieldValue = isset($values[$fieldMachine]) ? $values[$fieldMachine] : null;
            // Hydrate the field
            $hydratedField = $field->getHydratedField($fieldValue);
            // Add the field into the hydrated group
            $hydrated->push($hydratedField);
        }
        // Return the hydrated collection
        $this->fields = $hydrated;
        // Rebuild the field index
        $this->getIndexer()->build();
        // Return for chaining
        return $this;
    }

    public function getIndexer() {
        // Return the indexer instance
        return $this->indexer;
    }

    /**
     * Get Meta Field
     * @param $path
     * @param string $format
     * @return mixed
     */
    public function getField($path,$format='value') {
        // Retrieve the current field indexer
        $fieldIndexer = $this->getIndexer();
        // Retrieve the field object index
        $fieldObjects = $fieldIndexer->toObjects();
        // If the field does not exist then return null
        if (!isset($fieldObjects[$path])) { return null; }
        // Retrieve the field object from the field indexer
        $field = $fieldObjects[$path];
        // Return the required field value
        switch(strtolower($format)) {
            // Return formatted values
            case 'default' : return $field->getDefault(true); break;
            case 'value' : return $field->getValue(true); break;
            case 'raw' : return $field->getValue(false); break;
            case 'object' : return $field; break;
            // Return the formatted value by default
            default : return $field->getValue(true); break;
        }
    }

    /**
     * Set Meta Field
     * @param $path
     * @param $value
     * @return mixed
     */
    public function setField($path,$value) {
        // Retrieve the current field indexer
        $fieldIndexer = $this->getIndexer();
        // Retrieve the field object index
        $fieldObjects = $fieldIndexer->toObjects();
        // If the field does not exist then return null
        if (!isset($fieldObjects[$path])) { return $this; }
        // Retrieve the field object from the field indexer
        $field = $fieldObjects[$path];
        // Set the field value
        $field->setValue($value,true);
        // Rebuild the field index
        $fieldIndexer->build();
        // Return for chaining
        return $this;
    }

}