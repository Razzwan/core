<?php
namespace liw\core\model;

use liw\core\Liw;
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
    public function labelFields(){return[];}

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

    /**
     * Если была ошибка, то в поле $this->error заполнится здесь
     * @return bool true, если валидация пройдена и false в противном случае
     */
    public function validate(){
        foreach ($this->rules() as $field => $arrRules){
            if(in_array($field, array_keys($this->fields))){
                foreach($arrRules as $key => $value){
                    if(is_int($value)){
                        if($this->error = call_user_func('liw\core\validation\Validate::' . $key, $this->fields[$field], $value)){
                            $this->error = Liw::$lang['error']['field'] . is_null($this->labelFields()[$field]) . $this->error;
                            return false;
                        }
                    }else{
                        if(method_exists($this, $value)){
                            if($this->error = call_user_func([$this, $value], $field, $this->fields[$field])){
                                $this->error = Liw::$lang['error']['field'] . $this->labelFields()[$field] . $this->error;
                                return false;
                            }
                        }else{
                            if($this->error = call_user_func('liw\core\validation\Validate::' . $value, $this->fields[$field])){
                                $this->error = Liw::$lang['error']['field'] . $this->labelFields()[$field] . $this->error;
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
                    if($value == $_SESSION['phrase']){
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
