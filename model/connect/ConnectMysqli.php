<?php
namespace liw\core\model\connect;

use liw\core\develop\Dev;
use liw\core\ErrorHandler;
use liw\core\Liw;
use liw\core\model\connect\ConnectInterface;

class ConnectMysqli implements ConnectInterface
{
    /**
     * @var object
     */
    protected static $_connection;

    /**
     * @var object
     */
    private $mysqli;

    /**
     * @throws \Exception
     */
    private function __construct(){
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
     * @return ConnectMysqli |object
     */
    public static function getConnection() {
        if (null === self::$_connection) {
            self::$_connection = new self();
        }
        return self::$_connection;
    }

    private function __clone(){}

    /**
     *
     */
    public function __destruct()
    {
        if(!(null === self::$_connection)) $this->mysqli->close();
    }

    private function simpleQuery($sql, $get_result = true, $get_id = false)
    {
        if( !($result = $this->mysqli->query($sql)) ){
            ErrorHandler::insertErrorInLogs("DB_ERROR[]", 'Не удалось отправить запрос', 'ConnectMysqli', '92');
            return false;
        }
        if ($get_result){
            return $result;
        }
        if (!$get_result && $get_id){
            return $this->mysqli->insert_id;
        }
        return true;
    }

    /**
     * @param $sql
     * @param $param
     * @param $get_result
     * @param bool|false $get_insert_id
     * @return bool|int|\mysqli_result
     */
    private function prepareQuery($sql, $param, $get_result, $get_insert_id = false)
    {
        if( (($stmt = $this->mysqli->stmt_init()) === false) ||
            (($stmt->prepare($sql)) === false) ||
            (call_user_func_array([$stmt, 'bind_param'], self::refValues($param)) === false) ||
            ($stmt->execute() === false)
        ){
            ErrorHandler::insertErrorInLogs("DB_ERROR[$stmt->errno]", $stmt->error, 'ConnectMysqli', '79');
            return false;
        }
        if($get_result){
            if(($result = $stmt->get_result()) === false){
                ErrorHandler::insertErrorInLogs("DB_ERROR[$stmt->errno]", $stmt->error, 'ConnectMysqli', '79');
                return false;
            }
        }
        if(!$get_result && $get_insert_id){
            if(($result = $stmt->insert_id) === false){
                ErrorHandler::insertErrorInLogs("DB_ERROR[$stmt->errno]", $stmt->error, 'ConnectMysqli', '79');
                return false;
            }
        }
        if($stmt->close() === false){
            ErrorHandler::insertErrorInLogs("DB_ERROR[$stmt->errno]", $stmt->error, 'ConnectMysqli', '79');
            return false;
        }
        if(isset($result) && $result){
            return $result;
        }
        return true;
    }

    /**
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
        if(!empty($param) && !empty($type_param)){
            array_unshift($param, $type_param);
            $result = $this->prepareQuery($sql, $param, true);
        } else {
            $result = $this->simpleQuery($sql);
        }

        return $result;
    }

    /**
     * @param $sql
     * @param null $type_param
     * @param array|null $param
     * @param $get_id boolean
     * @return bool|int|mixed
     */
    public function push($sql, $type_param = null, array $param = null, $get_id = true)
    {
        if(isset($param) && isset($type_param)){
            array_unshift($param, $type_param);
            $result = $this->prepareQuery($sql, $param, false, $get_id);
        } else {
            $result = $this->simpleQuery($sql, false, $get_id);
        }

        return $result;
    }

}
