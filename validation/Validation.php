<?php
namespace liw\core\validation;

use liw\core\Lang;

trait Validation {
    /**
     * Все переменные модели хранятся здесь
     * @var array
     */
    public $fields = [];

    /**
     * Лэйблы для переменных из fields хранятся здесь
     */
    public function labels()
    {
        return [];
    }

    /**
     * Хранит текст ошибки, или false, если ошибка отсутствует
     * @var
     */
    public $error = false;

    /**
     * Хранит правила, по которым происходит верификация
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    private function getLabel($field)
    {
        if(isset($this->labels()[$field])){
            return $this->labels()[$field];
        }
        return $field;
    }

    /**
     * Если была ошибка, то в поле $this->error заполнится здесь
     * @return bool true, если валидация пройдена и false в противном случае
     */
    public function validate(){
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
    }
}