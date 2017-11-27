<?php

namespace Sackrin\Meta\Model;

use Underscore\Types\Arrays;

trait HasMetaFields {

    public $metaFieldSchema;

    public static function bootHasMetaFields() {

        static::observe(MetaFieldEvents::class);
    }

    public function meta() {

        return $this->hasMany(static::$metaModel)->orderBy('position');
    }

    public function loadMetaFields() {
        // Retrieve all of the current meta objects
        $meta = $this->meta()->get();
        // Where we will store the decoded array
        $decoded = [];
        // Loop through each of the returned meta results
        foreach ($meta as $metaRecord) {
            // Use array set to convert from dot notation to a multi dimensional array
            array_set($decoded, $metaRecord->path, $metaRecord->value);
        }
        // Load the hydrated field schema instance into the fields property
        $this->metaFieldSchema = static::fieldSchema()
            ->hydrate($decoded);
        // Return for chaining
        return $this;
    }

    public function saveMetaFields() {
        // Retrieve the field objects
        $fieldObjects = $this->metaFieldSchema
            ->getHydrater()
            ->getIndexer()
            ->toObjects();
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
        foreach ($fieldObjects as $path => $fieldObject) {
            // Attempt to retrieve any existing meta record
            $existing = $meta->where('path', $path)->first();
            // Create or reuse the meta model instance
            $metaObject = $existing ? $existing : new static::$metaModel();
            // Populate the meta object values
            $metaObject->path = $path;
            $metaObject->position = $position;
            $metaObject->type = $fieldObject::$type;
            $metaObject->value = $fieldObject::serialize($fieldObject->getValue());
            // Save within the meta record owner
            $this->meta()->save($metaObject);
            // Push into the updated collection for housekeeping
            $updated->push($metaObject);
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

    public function getMetaField($path,$format='value') {
        // Retrieve the field schema
        $fieldSchema = $this->metaFieldSchema;
        // Return the hydrated field
        return $fieldSchema
            ->getHydrater()
            ->getField($path,$format);
    }

    public function setMetaField($path,$value) {
        // Retrieve the field schema
        $fieldSchema = $this->metaFieldSchema;
        // Return the hydrated field
        $fieldSchema
            ->getHydrater()
            ->setField($path,$value);
        // Return for chaining
        return $this;
    }

}