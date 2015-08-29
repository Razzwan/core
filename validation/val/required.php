<?php
use liw\core\validation\val\Val;

class Required implements Val
{
    static public function test($var = null, $value = null)
    {
        if(!is_array($var)){
            if (!isset($var) || $var === null || $var === '') {
                return 'required';
            }
            return true;
        } else {
            if(count($var) == 0 || (count($var) == 1 && array_shift($var) === null)){
                return 'required';
            } else {
                return true;
            }
        }

    }
}