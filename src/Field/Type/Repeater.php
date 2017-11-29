<?php

namespace Sackrin\Meta\Field\Type;

use Sackrin\Meta\Field\Field;

class Repeater extends Group {

    public static $defaults = [
        'machine' => '',
        'label' => '',
        'instructions' => ''
    ];

    public static $type = 'repeater';

    public function childReference(Field $field) {
        // Return the path with a placeholder for the repeater child
        return $this->getPath().'.x.'.$field->getMachine();
    }



    public function childPath(Field $field) {
        // Return the path with a placeholder for the repeater child
        return $this->getPath().'.'.$field->getPosition().'.'.$field->getMachine();
    }

    public function toPath($collection) {
        // Set the field in the index collection
        $collection->put($this->getPath(), $this);
        // Retrieve the fields which would be relevant
        $fieldGroups = $this->getHydrated();
        // Loop through each of the sub fields
        foreach ($fieldGroups as $fieldGroup) {
            // Convert the sub field to index
            // Loop through each of the sub fields
            foreach ($fieldGroup as $field) {
                // Convert the sub field to index
                $field->toPath($collection);
            }
        }
    }

    public function hydrate($values) {
        // Create a copy to hydrate
        $cloned = $this->cloneField();
        // Reset the fields to an empty collection
        $cloned->setHydrated(collect([]));
        // Inject the raw new values
        $cloned->value = is_array($values) ? $values : [];
        // Loop through each of the provided value groups
        foreach ($values as $k => $valueGroup) {
            // Create a new sub field group
            $fieldGroup = collect([]);
            // Loop through each of the fields
            foreach ($this->getBlueprints() as $_k => $field) {
                // Retrieve the field machine code
                $fieldMachine = $field->getMachine();
                // Retrieve the field value
                $fieldValue = isset($valueGroup[$fieldMachine]) ? $valueGroup[$fieldMachine] : null;
                // Hydrate the field
                $clonedField = $field->hydrate($fieldValue);
                // Add the field into the hydrated group
                $fieldGroup->addHydrated($clonedField);
            }
            // Push the field into the cloned field object
            $cloned->getFields()->push($fieldGroup);
        }
        // Return the text value
        return $cloned;
    }

    public function setValue($values) {
        // Inject the raw new values
        $this->value = $values;
        // Reset the fields to an empty collection
        $this->setHydrated(collect([]));
        // Inject the raw new values
        $this->value = is_array($values) ? $values : [];
        // Loop through each of the provided value groups
        foreach ($values as $k => $valueGroup) {
            // Create a new sub field group
            $fieldGroup = collect([]);
            // Loop through each of the fields
            foreach ($this->getBlueprints() as $_k => $field) {
                // Retrieve the field machine code
                $fieldMachine = $field->getMachine();
                // Retrieve the field value
                $fieldValue = isset($valueGroup[$fieldMachine]) ? $valueGroup[$fieldMachine] : null;
                // Hydrate the field
                $clonedField = $field->hydrate($fieldValue);
                // Set the field position
                $clonedField->setPosition($k);
                // Add the field into the hydrated group
                $fieldGroup->addHydrated($clonedField);
            }
            // Push the field into the cloned field object
            $this->getFields()->push($fieldGroup);
        }
        // Return for chaining
        return $this;
    }

}