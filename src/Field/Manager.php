<?php

namespace Sackrin\Meta\Field;

class Manager {

    public $model;

    public function __construct($model) {
        // Update the model
        $this->model = $model;
    }

    public function injectValues() {

    }

}