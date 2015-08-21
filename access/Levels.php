<?php
namespace liw\access;

class Levels
{
    static private $level = '';

    static private function strToAccess()
    {

    }

    static public function getAccess($level = '')
    {
        if (empty($level)){
            return false;
        }
        self::$level = $level;
        return self::strToAccess();
    }
}