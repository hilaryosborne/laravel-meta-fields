<?php

namespace Sackrin\Meta\Model;

use Sackrin\Meta\Field\Hydrater;

trait HasMetaFields {

    public $metaFields;

    public static function bootHasMetaFields() {
        // Attach the meta field observer
        static::observe(MetaFieldEvents::class);
    }

    public function meta() {

        return $this->hasMany(static::$metaModel)->orderBy('position');
    }

    public function getMetaFields() {
        // Retrieve the meta fields instance
        return $this->metaFields;
    }

    public function loadMetaFields() {
        // Retrieve the model's blueprint object
        $blueprint = static::fieldMetaBlueprint();
        // Retrieve all of the current meta objects
        $meta = $this->meta()->get();
        // Where we will store the decoded array
        $flattened = [];
        // Loop through each of the returned meta results
        foreach ($meta as $metaRecord) {
            // Retrieve the field object by the route
            $fieldObject = $blueprint->getBlueprint($metaRecord->reference);
            // If no field is present then skip this value
            if (!$fieldObject) { continue; }
            // Use array set to convert from dot notation to a multi dimensional array
            array_set($flattened, $metaRecord->path, $fieldObject::unserialize($metaRecord->value));
        }
        // Load the hydrated field schema instance into the fields property
        $this->metaFields = (new Hydrater())->setBlueprint($blueprint)->hydrate($flattened);
        // Return for chaining
        return $this;
    }

    public function saveMetaFields() {
        // Retrieve the field objects
        $fieldObjects = $this->getMetaFields()->toObjects();
        // Retrieve the current meta objects
        $meta = $this->meta()->get();
        // Create a housekeeping collection
        // We want to ensure any meta models, no longer required, are deleted
        $updated = collect([]);
        // Position incremental
        // To preserve meta value integrity we will rebuild this each time
        $position = 0;
        // Retrieve the primary keys
        $metaPrimaryKey = (new static::$metaModel())->getKeyName();
        // Build the meta instance objects
        // Loop through each of the field objects
        foreach ($fieldObjects as $path => $field) {
            // Attempt to retrieve any existing meta record
            $existing = $meta->where('path', $path)->first();
            // Create or reuse the meta model instance
            $meta = $existing ? $existing : new static::$metaModel();
            // Populate the meta object values
            $meta->machine = $field->getMachine();
            $meta->reference = $field->getReference();
            $meta->path = $field->getPath();
            $meta->position = $position;
            $meta->type = $field::$type;
            $meta->value = $field->serialize();
            // Save within the meta record owner
            $this->meta()->save($meta);
            // Push into the updated collection for housekeeping
            $updated->push($meta);
            // Increment up the position
            $position++;
        }
        // Create a removed collection
        // We will use this to delete any unused meta objects
        $meta->each(function ($fieldObject, $key) use ($updated, $metaPrimaryKey) {
            // Search for this object in the updated collection
            $updatedMetaObject = $updated->where($metaPrimaryKey, $fieldObject->id)->first();
            // Return this meta object if no meta object was found
            if ($updatedMetaObject) { return true; }
            // Delete the meta object
            $fieldObject->delete();
        });
        // Return for chaining
        return $this;
    }

    /**
     * Get Field
     * @param $path
     * @param string $format
     * @return mixed
     */
    public function getField($path,$format='value') {
        // Retrieve the field object index
        $fieldObjects = $this->getMetaFields()->toObjects();
        // If the field does not exist then return null
        if (!isset($fieldObjects[$path])) { return null; }
        // Retrieve the field object from the field index
        $field = $fieldObjects[$path];
        // Return the required field value
        switch(strtolower($format)) {
            // Return field values based on desired format
            case 'default' : return $field->getDefault(true); break;
            case 'value' : return $field->getValue(true); break;
            case 'raw' : return $field->getValue(false); break;
            case 'object' : return $field; break;
            // Return the formatted value by default
            default : return $field->getValue(true); break;
        }
    }

    /**
     * Set Field
     * @param $path
     * @param $value
     * @return mixed
     */
    public function setField($path,$value) {
        // Retrieve the field object index
        $fieldObjects = $this->getMetaFields()->toObjects();
        // If the field does not exist then return null
        if (!isset($fieldObjects[$path])) { return $this; }
        // Retrieve the field object from the field indexer
        $field = $fieldObjects[$path];
        // Set the field value
        $field->setValue($value,true);
        // Rebuild the field index
        $this->getMetaFields()->reIndex();
        // Return for chaining
        return $this;
    }

}