<?php
namespace liw\core\base\model;

use liw\core\Liw;

class BaseModel
{
    /**
     * Ассоциативный массив полей и значений модели
     * @var array
     */
    public $fields = [];

    /**
     * Массив лэйблов для названия полей модели
     */
    public function labelFields(){return[];}

    /**
     * Текст ошибки, если она произошла, или false, в случае ее отсутствия
     * @var
     */
    public $error = false;

    /**
     * Правила для полей в таблице
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * Присваиваем $this->error текст ошибки
     * @return bool истина в случае успешной валидации, ложь - в случае ошибки
     */
    public function validate(){
        foreach ($this->rules() as $field => $arrRules){
            if(in_array($field, array_keys($this->fields))){
                foreach($arrRules as $key => $value){
                    if(is_int($value)){
                        if($this->error = call_user_func('liw\core\base\validation\Validate::' . $key, $this->fields[$field], $value)){
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
                            if($this->error = call_user_func('liw\core\base\validation\Validate::' . $value, $this->fields[$field])){
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
     * Производит валидацию массива пост и присваивает свойству $fields данные из массива $_POST,
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
            }
            return $this->validate(); //возвращает истину в случае успешной валидации, иначе - ложь
        }
        return false;
    }

}
