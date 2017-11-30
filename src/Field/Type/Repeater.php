<?php

namespace Sackrin\Meta\Field\Type;

use Illuminate\Support\Collection;
use Sackrin\Meta\Field\Field;

class Repeater extends Group {

    public static $defaults = [
        'machine' => '',
        'label' => '',
        'instructions' => ''
    ];

    public static $type = 'repeater';

    public function childReference(Field $field) {
        // Return the path with a placeholder for the repeater childs position
        return $this->getPath().'.x.'.$field->getMachine();
    }

    public function childPath(Field $field) {
        // Return the path with the position for the repeater child
        return $this->getPath().'.'.$field->getPosition().'.'.$field->getMachine();
    }

    public function toPath(Collection $collection) {
        // Set the field in the index collection
        $collection->put($this->getPath(), $this);
        // Retrieve the hydrated fields
        $HydratedGroups = $this->getHydrated();
        // Loop through each of the field groups
        // For repeaters the hydrated fields contain individual groups of fields
        foreach ($HydratedGroups as $HydratedGroup) {
            // Loop through each of the hydrated group's fields
            foreach ($HydratedGroup as $hydrated) {
                // Pass the field through the to path process
                $hydrated->toPath($collection);
            }
        }
        // Return for chaining
        return $this;
    }

    public function hydrate($values) {
        // Create a copy to hydrate
        $cloned = $this->cloneField();
        // Reset the hydrated fields to an empty collection
        $cloned->setHydrated(collect([]));
        // Add the passed values
        $cloned->value = is_array($values) ? $values : [];
        // Loop through each of the provided values
        foreach ($cloned->value as $position => $valueGroup) {
            // Create a new sub field group
            $fieldGroup = collect([]);
            // Loop through each of the blueprints
            foreach ($this->getBlueprints() as $_k => $blueprint) {
                // Retrieve the blueprint machine code
                $fieldMachine = $blueprint->getMachine();
                // Retrieve the field value from the passed values
                $fieldValue = isset($valueGroup[$fieldMachine]) ? $valueGroup[$fieldMachine] : null;
                // Hydrate and return a new field instance
                $clonedField = $blueprint->hydrate($fieldValue);
                // Set the hydrated field's parent field
                $clonedField->setParent($cloned);
                // Set the field group's position
                // This will be used to group fields within the toPath index
                $clonedField->setPosition($position);
                // Add the field into the hydrated field group
                $fieldGroup->push($clonedField);
            }
            // Push the field into the cloned field object
            $cloned->getHydrated()->push($fieldGroup);
        }
        // Return the cloned field instance
        return $cloned;
    }

    public function setValue($values) {
        // Reset the hydrated fields to an empty collection
        $this->setHydrated(collect([]));
        // Add the passed values
        $this->value = is_array($values) ? $values : [];
        // Loop through each of the provided value groups
        foreach ($values as $position => $valueGroup) {
            // Create a new sub field group
            $fieldGroup = collect([]);
            // Loop through each of the fields
            foreach ($this->getBlueprints() as $_k => $blueprint) {
                // Retrieve the blueprint machine code
                $fieldMachine = $blueprint->getMachine();
                // Retrieve the field value from the passed values
                $fieldValue = isset($valueGroup[$fieldMachine]) ? $valueGroup[$fieldMachine] : null;
                // Hydrate and return a new field instance
                $clonedField = $blueprint->hydrate($fieldValue);
                // Set the hydrated field's parent field
                $clonedField->setParent($this);
                // Set the field group's position
                // This will be used to group fields within the toPath index
                $clonedField->setPosition($position);
                // Add the field into the hydrated field group
                $fieldGroup->push($clonedField);
            }
            // Push the field into this fields hydrated groups
            $this->getHydrated()->push($fieldGroup);
        }
        // Return for chaining
        return $this;
    }

}