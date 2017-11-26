<?php

namespace Sackrin\Meta\Field\Type;

class Repeater extends Group {

    public static $defaults = [
        'machine' => '',
        'label' => '',
        'instructions' => ''
    ];

    public static $type = 'repeater';

    public function getChildPath($field) {
        // Return the path with a placeholder for the repeater child
        return $field->hydrated ? $this->getPath().'.'.$field->getPosition().'.'.$field->getMachine() : $this->getPath().'.x.'.$field->getMachine();
    }

    public function toIndex($collection) {
        // Set the field in the index collection
        $collection->put($this->getPath(), $this);
        // Retrieve the fields which would be relevant
        $fields = $this->getHydrated() ? $this->getFields() : $this->getTemplates() ;

        if ($this->hydrated) {
            // Loop through each of the sub fields
            foreach ($fields as $fieldGroup) {
                // Convert the sub field to index
                // Loop through each of the sub fields
                foreach ($fieldGroup as $field) {
                    // Convert the sub field to index
                    $field->toIndex($collection);
                }
            }
        } else {
            // Loop through each of the sub fields
            foreach ($fields as $k => $field) {
                // Convert the sub field to index
                $field->toIndex($collection);
            }
        }
    }

    public function getHydratedField($values,$test=false) {
        // Create a copy to hydrate
        $cloned = $this->cloneField();
        // Reset the fields to an empty collection
        $cloned->setFields(collect([]));
        // Loop through each of the provided value groups
        foreach ($values as $k => $valueGroup) {

            $fieldGroup = collect([]);

            foreach ($this->getTemplates() as $_k => $field) {
                // Retrieve the field machine code
                $fieldMachine = $field->getMachine();
                // Retrieve the field value
                $fieldValue = isset($valueGroup[$fieldMachine]) ? $valueGroup[$fieldMachine] : null;
                // Hydrate the field
                $clonedField = $field->getHydratedField($fieldValue);
                // Set the field parent
                $clonedField->setParentField($cloned);

                $clonedField->position = $k;
                // Add the field into the hydrated group
                $fieldGroup->push($clonedField);
            }
            // Push the field into the cloned field object
            $cloned->getFields()->push($fieldGroup);
        }
        // Inject the raw new values
        $cloned->setValue($values);
        // Set that this is a hydrated field object
        $cloned->setHydrated(true);
        // Return the cloned instance value
        return $cloned;
    }

    public static function serialize($value) {

        return count($value);
    }

    public static function unserialize($value) {

        return null;
    }

    public function getValue($formatted=true) {
        // Initially just return a raw value
        return $this->value;
    }

}