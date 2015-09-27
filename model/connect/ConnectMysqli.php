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

    private function simpleQuery($sql, $get_insert_id = false)
    {
        if( !($result = $this->mysqli->query($sql)) ){
            ErrorHandler::insertErrorInLogs("DB_ERROR[]", 'Не удалось отправить запрос', 'ConnectMysqli', '92');
            return false;
        }
        if ($get_insert_id){
            return $this->mysqli->insert_id;
        }
        return $result;
    }

    /**
     * @param $sql
     * @param $param
     * @param bool|false $get_insert_id
     * @return bool|int|\mysqli_result
     */
    private function prepareQuery($sql, $param, $get_insert_id = false)
    {
        $stmt = $this->mysqli->stmt_init();
        if(
            (($stmt->prepare($sql)) === false) ||
            (call_user_func_array([$stmt, 'bind_param'], self::refValues($param)) === false) ||
            ($stmt->execute() === false) ||
            (($result = $stmt->get_result()) === false)
        ){
            ErrorHandler::insertErrorInLogs("DB_ERROR[$stmt->errno]", $stmt->error, 'ConnectMysqli', '87');
            return false;
        }
        if($get_insert_id){
            if(($result = $stmt->insert_id) === false){
                ErrorHandler::insertErrorInLogs("DB_ERROR[$stmt->errno]", $stmt->error, 'ConnectMysqli', '92');
                return false;
            }
        }
        $stmt->close();

        if(isset($result)){
            return $result;
        }
        return true;
    }

    /**
     * @param $sql
     * @param array|null $params
     * @param bool|false $get_insert_id
     * @return bool|int|mixed|\mysqli_result
     * @throws \Exception
     */
    public function query($sql, array $params = null, $get_insert_id = false)
    {
        $params = $this->addTypesParam($params);

        if(defined("DEVELOP") && DEVELOP === true){
            Dev::$dev['requests'][] = $sql . ' [' . implode(', ', $params) . '] get_id = ' . $get_insert_id;
        }

        if(!empty($param)){
            $result = $this->prepareQuery($sql, $params, $get_insert_id);
        } else {
            $result = $this->simpleQuery($sql, $get_insert_id);
        }

        return $result;
    }

    /**
     * @param $params
     * @throws \Exception
     * @return array
     */
    private function addTypesParam(array $params)
    {
        $type_param = '';
        foreach($params as $param){
            switch (gettype($param)){
                case 'boolean' : $type_param .= 'i';
                    break;
                case 'integer' : $type_param .= 'i';
                    break;
                case 'double' : $type_param .= 'd';
                    break;
                case 'string' : $type_param .= 's';
                    break;
                default:
                    throw new \Exception(
                        "Ошибка типа данных в конструкции " . $method. ", передан тип данных: " . gettype($value)
                    );
            }
        }
        return array_unshift($param, $type_param);

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
}
