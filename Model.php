<?php
namespace liw\core;

use liw\core\model\BaseModel;
use liw\core\model\Connect;

class Model extends BaseModel
{
    /**
     * Объект подключения к базе данных
     * @var Connect|object
     */
    private $bd;

    /**
     * @var string название таблицы в БД, с которой работаем
     */
    protected $table;

    /**
     * Заброс к БД
     * @var string
     */
    private $sql;

    /**
     * Параметры которые нужно забиндить к запросу к БД
     * @var array
     */
    private $bind_param = [];

    /**
     * @var string
     */
    private $type_param = '';

    public function __get($var)
    {
        if (isset($this->fields[$var])){
            return $this->fields[$var];
        } else {
            throw new \Exception('Неопределенная переменная ' . $var);
        }
    }

    public function __set($var, $value)
    {
        $this->fields[$var] = $value;
    }

    public function __unset($var)
    {
        unset($this->fields[$var]);
    }

    public function __isset($var)
    {
        return isset($this->fields[$var]);
    }

    public function __empty($var)
    {
        return empty($this->fields[$var]);
    }

    /**
     * создаем подключение к базе данных, если оно еще отсутствует.
     */
    public function connect()
    {
        $this->bd = Connect::getConnection();
    }

    public function query($sql, $bind_param = [])
    {
        $this->connect();
        $this->sql = $sql;
        $this->bind_param = [];
        $this->type_param = '';
        if(!empty($bind_param)){
            foreach ($bind_param as $key=>$value){
                array_push($this->bind_param, $value);
                $this->valueToChar($value, 'QUERY');
            }
        }
        return $this;
    }

    /**
     * ФОрмирует запрос к БД и возвращает true в случае успеха и обибку в случае ошибки
     * @return mixed
     * @throws \Exception
     */
    public function push()
    {
        $this->connect();
        //$this->echo_all();
        return $this->id = $this->bd->push($this->sql, $this->type_param, $this->bind_param);
    }

    /**
     * @return mixed 1. одномерный массив, если БД вернула строку,
     *               2. двумерный - если несколько строк,
     *               3. false - если вернула пустой результат
     *               4. в случае если произошла ошибка в БД - вернет ошибку "SQL ERROR"
     * @throws \Exception
     */
    public function get()
    {
        $this->connect();
        //$this->echo_all();
        $result = $this->bd->get($this->sql, $this->type_param, $this->bind_param);
        $num_rows = $result->num_rows;
        if($num_rows == 0){
            return 0;
        } elseif($num_rows==1){
            $result = $result->fetch_assoc();
            array_push($this->fields, $result);
            return $this->fields = array_merge($this->fields, $result);
        } else {
            $i = 0;
            while($str = $result->fetch_assoc()){
                $this->fields[$i]=$str;
                $i++;
            }
            return $this->fields;
        }
    }

    /**
     * Формирует соответствующий элемент запроса к БД
     * @param null $array
     * @return $this
     */
    public function select($array = null)
    {
        $this->sql = "SELECT";
        if(is_array($array)){
            foreach ($array as $value){
                $this->sql .= " `" . $value . "`,";
            }
            $this->sql = substr($this->sql, 0, -1);
        } else {
            $this->sql .= " *";
        }

        $this->sql .= " FROM `" . $this->table . "`";
        return $this;
    }

    /**
     * Формирует соответствующий элемент запроса к БД
     * @param $array
     * @return $this
     * @throws \Exception
     */
    public function where($array)
    {
        if(is_array($array)){
            $this->sql .= " WHERE";
            foreach ($array as $key => $value){
                $this->sql .= " `" . $key . "` = ? and";
                array_push($this->bind_param, $value);
                $this->valueToChar($value, 'WHERE');
            }
            $this->sql = substr($this->sql, 0, -4);
        }
        return $this;
    }

    /**
     * Формирует соответствующий элемент запроса к БД
     * @param $array
     * @return $this
     * @throws \Exception
     */
    public function insert ($array)
    {
        $this->type_param = '';
        $this->bind_param = [];
        $this->sql = "INSERT INTO `" . $this->table . "`(";
        if(is_array($array)){
            foreach ($array as $value){
                $this->sql .= $value . ", ";
            }
            $this->sql = substr($this->sql, 0, -2);
            $this->sql .= ") VALUES (";
            $this->arrToStrTypes($array, '?, ', 'INSERT');
            $this->sql = substr($this->sql, 0, -2);
            $this->sql .= ")";

        } else {
            /*throw new \Exception(
                "Нет данных для вставки"
            );*/
            //Добавить логирование
            return $this;
        }
        return $this;
    }

    /**
     * @param $array
     * @return $this
     * @throws \Exception
     */
    public function update($array)
    {
        $this->type_param = '';
        $this->bind_param = [];
        $this->sql = "UPDATE `" . $this->table . "` SET ";
        if(is_array($array)){
            foreach ($array as $value){
                $this->sql .= $value . " = ?, ";
            }
            $this->sql = substr($this->sql, 0, -2);
            $this->arrToStrTypes($array, '', 'UPDATE');
        } else {
            throw new \Exception(
                "Нет данных для вставки"
            );
        }
        return $this;
    }

    public function delete()
    {
        $this->type_param = '';
        $this->bind_param = [];
        $this->sql = "DELETE FROM `" . $this->table . "`";
        return $this;
    }

    /**
     * @param $field
     * @param bool|false $desc
     * @return $this
     */
    public function order($field, $desc = false)
    {
        $desc ? $this->sql .= ' ORDER BY `' . $field . '` ' . $desc : $this->sql .= ' ORDER BY ' . $field;
        return $this;
    }

    /**
     * @param int $int
     * @return $this
     */
    public function limit($int = 5)
    {
        $this->sql .= ' LIMIT ' . $int;
        return $this;
    }

    /**
     * Метод сохраняет в базе данных массив fields
     * @return mixed : 1. id - сохраненной модели, если у нее есть поле AI в случае успешного сохранение
     *                 2. true - в случае успешного сохранение, но в отсутствие поля с AI
     *                 3. false - в случае неудачного сохранения
     * @throws \Exception
     */
    public function save(){
        if(!empty($this->fields)){
            $arr = [];
            foreach($this->rules() as $key => $value){
                if(in_array('required', $value)){
                    $arr[] = $key;
                }
            }

            $arr = array_unique(array_merge($arr, array_keys($this->fields)));
            if($this->validate() && ($id = $this->insert($arr)->push())){
                if($id!==true) $this->fields['id'] = $id;
                return true;
            }else{
                return false;
            }
        } else {
            //throw new \Exception('No date to save!');
            //добавить логирование
            return false;
        }
    }


    /**
     * @param $field
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function unique($field, $value){
        if($this->select([$field])->where([$field=>$value])->get() === 0){
            return true;
        }
        return 'user_exist';
    }

    /**
     * @param $value
     * @param $method
     * @throws \Exception
     */
    private function valueToChar($value, $method)
    {
        switch (gettype($value)){
            case 'boolean' : $this->type_param .= 'i';
                break;
            case 'integer' : $this->type_param .= 'i';
                break;
            case 'double' : $this->type_param .= 'd';
                break;
            case 'string' : $this->type_param .= 's';
                break;
            default:
                throw new \Exception(
                    "Ошибка типа данных в конструкции " . $method. ", передан тип данных: " . gettype($value)
                );
        }
    }

    /**
     * @param $array
     * @param string $part_sql
     * @param $method
     * @throws \Exception
     */
    private function arrToStrTypes($array, $part_sql = '', $method)
    {
        foreach ($array as $value){
            $this->sql .= $part_sql;
            array_push($this->bind_param, $this->fields[$value]);
            $this->valueToChar($value, $method);
        }
    }

    private function echo_all()
    {
        var_dump($this->fields);
        echo '<br><br>';

        echo 'bind_param: ';
        var_dump($this->bind_param);
        echo '<br><br>';

        echo 'type_param: ';
        var_dump($this->type_param);
        echo '<br><br>';

        echo 'sql: ';
        echo $this->sql;
        echo '<br><br>';
        exit;
    }
}
