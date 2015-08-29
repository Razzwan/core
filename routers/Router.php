<?php
namespace liw\core\routers;

use liw\core\validation\Validate;
use liw\core\web\Request;

/**
 * Класс отвечает за маршрутизацию
 * Class SmartRouter
 * @package liw\core
 */
class Router
{
    /**
     * @param array|null $attr
     * @param array|null $get
     * @param array $way
     * @return array
     * @throws \Exception
     */
    static public function getWay(array $attr = null, array $get = null, array $way){
        if (isset($way['options'])){
            if(count($way['options']) != count($attr)){
                Request::$attr = [];
                return $way;
            }
            $i = 0;
            foreach ($way['options'] as $option => $regV ){
                if (Validate::regV($attr[$i], $regV)){
                    throw new \Exception('Variable <strong>' . $option . '</strong> does not comply!');
                }
            }
            if($get){
                Request::$attr = array_merge($attr, $get);
            } else {
                Request::$attr = $attr;
            }

        } else {
            if($get){
                Request::$attr = $get;
            } else {
                Request::$attr = [];
            }

        }
        return $way;
    }

}