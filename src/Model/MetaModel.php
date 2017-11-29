<?php

namespace Sackrin\Meta\Model;

interface MetaModel {

    /**
     * Retrieve the schema object for this model
     * This object should contain all of the meta fields for this model
     */
    public static function fieldMetaBlueprint();

}