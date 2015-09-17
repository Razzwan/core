<?php
namespace liw\core\model;

use liw\core\Lang;
use liw\core\web\Session;

class BaseModel
{
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
                            $this->error = Lang::$uage['error_field'] . $this->getLabel($field) . Lang::$uage['error_'.$error] . $value;
                            return false;
                        }
                    }else{
                        if(method_exists($this, $value)){
                            if(($error = call_user_func([$this, $value], $field, $this->fields[$field])) !== true){
                                $this->error = Lang::$uage['error_field'] . $this->getLabel($field) . Lang::$uage['error_'.$error];
                                return false;
                            }
                        }else{
                            if(($error = call_user_func('liw\core\validation\Is::' .  $value, $this->fields[$field])) !== true){
                                $this->error = Lang::$uage['error_field'] . $this->getLabel($field) . Lang::$uage['error_'. $error];
                                return false;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Сохраняет переменные из массива $_POST в $this->fields, затем проверяет на соответсвтие правилам rules
     *
     * @param  array
     * @return mixed
     */
    public function post()
    {
        if (!empty($_POST)){
            foreach($_POST as $key => $value){
                if(in_array($key, array_keys($this->rules()))){
                     $this->fields[$key] = $value;
                }
                if($key == 'captcha'){
                    if(isset($_SESSION['phrase']) && $value == $_SESSION['phrase']){
                        Session::delete('phrase');
                        continue;
                    } else {
                        $this->error = 'error code';
                        Session::delete('phrase');
                        return false;
                    }
                }
            }
            return $this->validate(); //возвращает true в случае удачной валидации и false в противном случае
        }
        return false;
    }

}
