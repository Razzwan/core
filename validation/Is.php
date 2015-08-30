<?php
namespace liw\core\validation;

class Is
{
    static public function required($var)
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

    static public function string($var)
    {
        if (is_string($var)) {
            return true;
        }
        return 'string';
    }


    static public function number($var)
    {
        if (is_numeric($var)) {
            return true;
        }
        return 'number';
    }


    static public function max($var, $max)
    {
        if (mb_strlen($var, 'UTF-8') <= $max) {
            return true;
        }
        return 'max';
    }


    static public function min($var, $min)
    {
        if (mb_strlen($var, 'UTF-8') >= $min) {
            return true;
        }
        return 'min';
    }

    static public function login($login)
    {
        $pattern = "/^[a-z0-9_\-@\.\+]+$/i";
        if(preg_match($pattern, $login, $matches)){
            return true;
        }
        return 'login';
    }

    static public function regV($var, $regV)
    {
        if(preg_match($regV, $var, $matches)){
            return true;
        }
        return 'regV';
    }
}