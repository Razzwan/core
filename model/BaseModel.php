<?php
namespace liw\core\model;

class BaseModel
{
    public $fields;

    public $error;

    public function rules()
    {
        return [];
    }

    public function labels()
    {
        return [];
    }

}
