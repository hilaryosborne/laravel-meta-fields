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

    public $fields = [];

    public function __construct($machine) {
        // Call the parent constructor
        parent::__construct($machine);
        // The field objects
        $this->fields = collect([]);
    }

    public function getChildPath($field) {

        return $this->getPath().'.'.$field->getMachine();
    }

    public function addField($field) {
        // Set the field parent
        $field->setParentField($this);
        // Add to the fields collection
        $this->fields->push($field);
        // Return for chaining
        return $this;
    }

    public function toIndex($collection) {
        // Set the field in the index collection
        $collection->put($this->getPath(), $this);
        // Loop through each of the sub fields
        foreach ($this->fields as $k => $field) {
            // Convert the sub field to index
            $field->toIndex($collection);
        }
    }

    public function hydrate($values) {
        // Create a copy to hydrate
        $hydrated = $this->copy();
        // Loop through each of the fields
        foreach ($this->fields as $k => $field) {
            // Retrieve the field machine code
            $fieldMachine = $field->getMachine();
            // Retrieve the field value
            $fieldValue = isset($values[$fieldMachine]) ? $values[$fieldMachine] : null;
            // Hydrate the field
            $hydratedField = $field->hydrate($fieldValue);
            // Add the field into the hydrated group
            $hydrated->addField($hydratedField);
        }
        // Set that this is a hydrated field
        $hydrated->setHydrated(true);
        // Return the text value
        return $hydrated;
    }

    public function getValue($formatted=true) {
        // Initially just return a raw value
        return $this->value;
    }

}