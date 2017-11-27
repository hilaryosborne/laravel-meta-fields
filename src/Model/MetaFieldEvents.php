<?php

namespace Sackrin\Meta\Model;

class MetaFieldEvents {

    public function retrieved($model)
    {
        $model->loadMetaFields();
    }

    public function saving($model)
    {
        $model->saveMetaFields();
    }

    public function deleted($model)
    {

    }

}