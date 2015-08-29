<?php
class Max implements \liw\core\validation\val\Val
{
    static public function test($var = null, $max = null)
    {
        if (mb_strlen($var, 'UTF-8') <= $max) {
            return true;
        }
        return 'max';
    }
}