<?php
class Login implements \liw\core\validation\val\Val
{
    static public function test($var = null, $value = null)
    {
        $pattern = "/^[a-z0-9_\-@\.\+]+$/i";
        if(preg_match($pattern, $var, $matches)){
            return true;
        }
        return 'login';
    }
}