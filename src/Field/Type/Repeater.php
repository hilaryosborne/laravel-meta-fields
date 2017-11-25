<?php

namespace Sackrin\Meta\Field\Type;

class Repeater extends Group {

    public static $defaults = [
        'machine' => '',
        'label' => '',
        'instructions' => ''
    ];

    public static $type = 'repeater';

    public $value = [];

    public $fields = [];

    public function __construct($machine) {
        // Call the parent constructor
        parent::__construct($machine);
        // The field objects
        $this->fields = collect([]);
    }

    public function addField($field) {
        // Set the field parent
        $field->setParentField($this);
        // Add to the fields collection
        $this->fields->push($field);
        // Return for chaining
        return $this;
    }

    public function getChildPath($field) {
        // Return the path with a placeholder for the repeater child
        return $field->hydrated ? $this->getPath().'.'.$field->getPosition().'.'.$field->getMachine() : $this->getPath().'.x.'.$field->getMachine();
    }

    public function toIndex($collection) {
        // Set the field in the index collection
        $collection->put($this->getPath(), $this);

        if ($this->hydrated) {
            // Loop through each of the sub fields
            foreach ($this->fields as $fieldGroup) {
                // Convert the sub field to index
                // Loop through each of the sub fields
                foreach ($fieldGroup as $field) {
                    // Convert the sub field to index
                    $field->toIndex($collection);
                }
            }
        } else {
            // Loop through each of the sub fields
            foreach ($this->fields as $k => $field) {
                // Convert the sub field to index
                $field->toIndex($collection);
            }
        }
    }

    public function hydrate($values) {
        // Create a copy to hydrate
        $hydrated = $this->copy();

        $hydrated->fields = collect([]);
        // Loop through each of the fields
        foreach ($values as $k => $valueGroup) {

            $fieldGroup = collect([]);

            foreach ($this->fields as $_k => $field) {
                // Retrieve the field machine code
                $fieldMachine = $field->getMachine();
                // Retrieve the field value
                $fieldValue = isset($valueGroup[$fieldMachine]) ? $valueGroup[$fieldMachine] : null;
                // Hydrate the field
                $hydratedField = $field->hydrate($fieldValue);
                // Set the field parent
                $hydratedField->setParentField($this);

                $hydratedField->position = $k;
                // Add the field into the hydrated group
                $fieldGroup->push($hydratedField);
            }

            $hydrated->fields->push($fieldGroup);
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