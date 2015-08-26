<?php
namespace liw\core\validation;

use liw\core\Liw;

class Validate
{
    static public function required($var)
    {
        if (isset($var) && $var != '') {
            return false;
        }
        return Liw::$lang['error']['required'];
    }

    static public function string($var)
    {
        if (is_string($var)) {
            return false;
        }
        return Liw::$lang['error']['string'];
    }


    static public function number($var)
    {
        if (is_numeric($var)) {
            return false;
        }
        return Liw::$lang['error']['number'];
    }


    static public function max($var, $max)
    {
        if (mb_strlen($var, 'UTF-8') <= $max) {
            return false;
        }
        return Liw::$lang['error']['max'] . $max;
    }


    static public function min($var, $min)
    {
        if (mb_strlen($var, 'UTF-8') >= $min) {
            return false;
        }
        return Liw::$lang['error']['min'] . $min;
    }

    static public function login($login)
    {
        $pattern = "/^[a-z0-9_\-@\.\+]+$/i";
        if(preg_match($pattern, $login, $matches)){
            return false;
        }
        return Liw::$lang['error']['login'];
    }

    static public function regV($var, $regV)
    {
        if(preg_match($regV, $var, $matches)){
            return false;
        }
        return Liw::$lang['error']['login'];
    }
}