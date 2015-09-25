<?php
namespace liw\core\model;

use liw\core\develop\Dev;
use liw\core\Liw;

class Connect
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
     *
     */
    public function __destruct()
    {
        if(!(null === self::$_connection)) $this->mysqli->close();
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
        if(defined("DEVELOP") && DEVELOP===true){
            Dev::$dev['requests'][]=$sql;
        }
        if(!empty($param) && !empty($type_param)){
            array_unshift($param, $type_param);
            if(
                (($stmt = $this->mysqli->stmt_init())===false) or
                (($stmt->prepare($sql))===false) or
                (call_user_func_array([$stmt, 'bind_param'], self::refValues($param)) === FALSE) or
                ($stmt->execute()===false) or
                (($result = $stmt->get_result())===false) or
                ($stmt->close()===false)
            ){
                //throw new \Exception("SQL error: " . $stmt->error);
                return false;
            }

        } else {
            $result = $this->mysqli->query($sql);
        }
        if($result){
            return $result;
        } else {
            return false;
        }
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
            Dev::$dev['requests'][]=$sql;
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

    private function __clone(){}

    /**
     * @return Connect |object
     */
    public static function getConnection() {
        if (null === self::$_connection) {
            self::$_connection = new self();
        }
        return self::$_connection;
    }
}
