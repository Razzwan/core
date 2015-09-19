<?php
namespace liw\core\model;

use liw\core\Lang;
use liw\core\validation\Validation;
use liw\core\web\Session;

class BaseModel
{
    use Validation;

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
