<?php
namespace liw\core;

class Lang
{
    static public $uage = [];

    static public function add($array)
    {
        self::$uage = array_merge(self::$uage, $array);
    }
}