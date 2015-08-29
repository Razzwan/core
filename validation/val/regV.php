<?php
use liw\core\validation\val\Val;

class RegV implements Val
{
    static public function test($var = null, $regV = null)
    {
        if(preg_match($regV, $var, $matches)){
            return true;
        }
        return 'regV';
    }
}