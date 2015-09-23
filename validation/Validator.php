<?php
namespace liw\core\validation;

use liw\core\Lang;

trait Validator {

    private function getLabel($field)
    {
        if(isset($this->labels()[$field])){
            return $this->labels()[$field];
        }
        return $field;
    }

    public function validate()
    {
        if (!empty($this->filds) && !empty($this->rules())){
            foreach ($this->rules as $field => $rulesArr){
                $this->testField($field, $rulesArr);
            }
        }
        return 1; //возвращает 1, если нечего валидировать
    }

    private function testField($field, $rulesArr)
    {
        foreach($rulesArr as $key => $value) {

            if (method_exists($this, $value)) {

                $error = call_user_func([$this, $value], $field);

            } elseif (is_int($value)) {

                $error = call_user_func('liw\core\validation\Is::' . $key, $field, $value);

            } else {

                $error = call_user_func('liw\core\validation\Is::' . $value, $field);

            }
            if ($error !== true) {

                $this->error = Lang::uage('error_field') . $this->getLabel($field) . Lang::uage('error_' . $error) . $value;
                return false;

            }
            return true;
        }
    }

    /**
     * Если была ошибка, то в поле $this->error заполнится здесь
     * @return bool true, если валидация пройдена и false в противном случае
     */
    /*public function validate(){
        foreach ($this->rules() as $field => $arrRules){
            if(in_array($field, array_keys($this->fields))){
                foreach($arrRules as $key => $value){
                    if(is_int($value)){
                        if(($error = call_user_func('liw\core\validation\Is::' . $key, $this->fields[$field], $value)) !== true){
                            $this->error = Lang::uage('error_field') . $this->getLabel($field) . Lang::uage('error_'.$error) . $value;
                            return false;
                        }
                    }else{
                        if(method_exists($this, $value)){
                            if(($error = call_user_func([$this, $value], $field, $this->fields[$field])) !== true){
                                $this->error = Lang::uage('error_field') . $this->getLabel($field) . Lang::uage('error_'.$error);
                                return false;
                            }
                        }else{
                            if(($error = call_user_func('liw\core\validation\Is::' .  $value, $this->fields[$field])) !== true){
                                $this->error = Lang::uage('error_field') . $this->getLabel($field) . Lang::uage('error_'. $error);
                                return false;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }*/
}
