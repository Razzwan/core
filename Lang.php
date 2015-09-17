<?php
namespace liw\core;

class Lang
{
    static private $_ = [];

    static public function add($arr)
    {
        self::$_ = array_merge(self::$_, $arr);
    }

    static public function uage($_)
    {
        if(isset(self::$_[$_])){
            return self::$_[$_];
        }
        return '?' . $_ . '?';
    }
}