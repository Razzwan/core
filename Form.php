<?php
namespace liw\core;

use liw\core\model\BaseModel;
use liw\core\validation\Validator;
use liw\core\web\Session;

class Form extends BaseModel
{
    use Validator;

    /**
     * Сохраняет переменные из массива $_POST в $this->fields, затем проверяет на соответсвтие правилам rules
     *
     * @param  array
     * @return mixed
     */
    public function post()
    {
        if (isset($_SESSION['phrase'])){
            if(!empty($_POST)){
                if(!isset($_POST['captcha']) || $_POST['captcha'] !== $_SESSION['phrase']){
                    Session::delete('phrase');
                    $this->error = 'error code';
                    return false;
                }

                Session::delete('phrase');
                foreach($_POST as $key => $value){
                    $this->fields[$key] = $value;
                }

                return $this->validate(); //возвращает true в случае удачной валидации и false в противном случае
            }

        } else {
            foreach($_POST as $key => $value){
                $this->fields[$key] = $value;
            }

            return $this->validate();
        }
        return false;
    }
}
