<?php
namespace liw\core\validation;

class Is
{
    static public function valid($validator, $var, $value = null){
        if(is_file(LIW_CORE . 'core/validation/val/' . $validator .'.php')){
            require_once LIW_CORE . 'core/validation/val/' . $validator .'.php';
            $class = ucfirst($validator);
            return call_user_func($class . '::test', $var, $value);
        }
    }

}