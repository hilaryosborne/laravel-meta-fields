<?php

namespace Sackrin\Meta\Field\Templater;

use Sackrin\Meta\Field\FieldCollection;

class Collection implements FieldCollection {

    public $indexer;

    public $fields = [];

    public function __construct() {
        // The field objects
        $this->fields = collect([]);
        // Create a new indexerer
        $this->indexer = new Indexer($this);
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

    public function getIndexer() {
        // Retrieve the indexer instance
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

}