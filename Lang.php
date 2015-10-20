<?php
namespace liw\core;

class Lang
{
    static private $_ = [];

    static public function add($arr)
    {
        self::$_ = array_merge($arr, self::$_);
    }

    static public function uage($_)
    {
        if(isset(self::$_[$_])){
            return self::$_[$_];
        }
        /**
         * закомментировать следующую линию, если не нужно автозаполнение файлов
         */
        //self::insertLine($_);
        return '?' . $_ . '?';
    }

    static private function insertLine($field)
    {
        if(defined("DEVELOP") && DEVELOP === true){
            $file = LIW_CORE . "core/develop/lang/lang.php";
            $lines = file($file);
            $lines[sizeof($lines)-1] = "    '{$field}' => '$field',\n];";
            file_put_contents($file, implode("", $lines));
        }
    }

}
