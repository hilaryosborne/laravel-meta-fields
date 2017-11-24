<?php

namespace Sackrin\Meta\Model;

use Sackrin\Meta\Observer\MetaFieldManage;

trait HasMetaFields {

    public function bootUseMetaFields() {

        static::observe(new MetaFieldManage());
    }

    public function meta()
    {
        return $this->hasMany(static::$metaModel);
    }

}