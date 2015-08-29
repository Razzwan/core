<?php
class Min implements \liw\core\validation\val\Val
{
    static public function test($var = null, $min = null)
    {
        if (mb_strlen($var, 'UTF-8') >= $min) {
            return true;
        }
        return 'min';
    }
}