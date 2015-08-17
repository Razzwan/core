<?php
namespace liw\core\base\model;

use liw\core\Liw;

class ConnectPDO
{
    /**
     * @var object
     */
    protected static $_connection;

    /**
     * @var object
     */
    private $pdo;

    /**
     * @throws \Exception
     */
    public function __construct(){
        $this->pdo = new \PDO("mysql:
            host=". Liw::$config['db']['host'] . ";
            dbname=" . Liw::$config['db']['name'],
            Liw::$config['db']['user'],
            Liw::$config['db']['pass']
        );
        echo '<pre>';
        print_r(get_class_methods($this->pdo));
        echo '<pre>';
        exit;
        $this->mysqli = new \mysqli(
            Liw::$config['db']['host'],
            Liw::$config['db']['user'],
            Liw::$config['db']['pass'],
            Liw::$config['db']['name']
        );

        if ($this->mysqli->connect_error) {
            throw new \Exception(
                "Connection Error " . $this->mysqli->connect_errno . "<br>" . $this->mysqli->connect_error
            );
        }
        $this->mysqli->set_charset('utf8');
    }

    /**
     *
     */
    public function __destruct()
    {
        if(!(null === self::$_connection)) $this->mysqli->close();
    }

    /**
     * Создает ссылки из переменных
     * @param $arr
     * @return array
     */
    private static function refValues($arr)
    {
        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

    /**
     * @param $sql
     * @param null $type_param
     * @param array|null $param
     * @return bool|\mysqli_result
     * @throws \Exception
     */
    public function get($sql, $type_param = null, array $param = null)
    {
        if(defined("DEVELOP") && DEVELOP===true){
            Liw::$dev['requests'][]=$sql;
        }
        if(!empty($param) && !empty($type_param)){
            array_unshift($param, $type_param);
            if(
                (($stmt = $this->mysqli->stmt_init())===false) or
                (($stmt->prepare($sql))===false) or
                (call_user_func_array([$stmt, 'bind_param'], self::refValues($param)) === FALSE) or
                ($stmt->execute()===false) or
                ($stmt->bind_result($result)===false) or
                ($stmt->close()===false)
            ){
                //throw new \Exception("SQL error: " . $stmt->error);
                //�����������
                return false;
            }

        } else {
            $result = $this->mysqli->query($sql);
        }

        return $result;
    }

    /**
     * @param $sql
     * @param null $type_param
     * @param array|null $param
     * @return bool|int|mixed
     */
    public function push($sql, $type_param = null, array $param = null)
    {
        if(defined("DEVELOP") && DEVELOP===true){
            Liw::$dev['requests'][]=$sql;
        }
        if(isset($param) && isset($type_param)){
            array_unshift($param, $type_param);
            if(
                (($stmt = $this->mysqli->stmt_init())===false) or
                (($stmt->prepare($sql))===false) or
                (call_user_func_array([$stmt, 'bind_param'], self::refValues($param)) === FALSE) or
                ($stmt->execute()===false)
            ){
                //throw new \Exception("SQL error: " . $stmt->error);
                return false;
            }
            $result = $stmt->insert_id;
            if($stmt->close()===false){
                //throw new \Exception("SQL error: " . $stmt->error);
                return false;
            }
            return $result?:true;
        } else {
            if($this->mysqli->query($sql)){
                $result = $this->mysqli->insert_id;
                return $result?:true;
            }
            return false;
        }
    }

    /**
     * ������ ������������
     */
    private function __clone(){}

    /**
     * @return Connect |object
     */
    public static function getInstance() {
        // ��������� ������������ ����������
        if (null === self::$_connection) {
            // ������� ����� ���������
            self::$_connection = new self();
        }
        // ���������� ��������� ��� ������������ ���������
        return self::$_connection;
    }
}