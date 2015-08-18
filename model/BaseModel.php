<?php
namespace liw\core\model;

use liw\core\Liw;

class BaseModel
{
    /**
     * ������������� ������ ����� � �������� ������
     * @var array
     */
    public $fields = [];

    /**
     * ������ ������� ��� �������� ����� ������
     */
    public function labelFields(){return[];}

    /**
     * ����� ������, ���� ��� ���������, ��� false, � ������ �� ����������
     * @var
     */
    public $error = false;

    /**
     * ������� ��� ����� � �������
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * ����������� $this->error ����� ������
     * @return bool ������ � ������ �������� ���������, ���� - � ������ ������
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
     * ���������� ��������� ������� ���� � ����������� �������� $fields ������ �� ������� $_POST,
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
            return $this->validate(); //���������� ������ � ������ �������� ���������, ����� - ����
        }
        return false;
    }

}
