<?php

namespace Sackrin\Meta\Field;

interface FieldCollection {

    public function addField($field);

    public function getField($path,$format);

    public function setFields($fields);

    public function getFields();

    public function getIndexer();

}