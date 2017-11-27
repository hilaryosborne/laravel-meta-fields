<?php

namespace Sackrin\Meta\Field;

use Sackrin\Meta\Field\Hydrater\Collection as HydraterCollection;
use Sackrin\Meta\Field\Templater\Collection as TemplaterCollection;
use Sackrin\Meta\Field\Type\Group;
use Underscore\Types\Arrays;

class Schema {

    public $templates;

    public $hydrater;

    public function __construct() {
        // Create a templated collection object
        $this->templates = new TemplaterCollection();
        // Create a hydrated collection object
        $this->hydrater = new HydraterCollection($this->templates);
    }

    public function addField($field) {
        // Add to the templated fields collection collection
        $this->templates->addField($field);
        // Return for chaining
        return $this;
    }

    public function getTemplates() {
        // Return the template instance
        return $this->templates;
    }

    public function getHydrater() {
        // Return the hydrater instance
        return $this->hydrater;
    }

    public function hydrate($values) {
        // Hydrate the schema object with the values
        $this->getHydrater()
            ->hydrateFields($values);
        // Return for chaining or future use
        return $this;
    }

}