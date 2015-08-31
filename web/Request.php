<?php
/**
 * Created by Razzwan.
 * Date: 25.08.15
 * Time: 20:33
 */
namespace liw\core\web;

use liw\core\validation\Clean;
use liw\core\validation\Is;

class Request
{
    /**
     * @var string основной маршрут
     */
    static public  $route = '/';

    /**
     * @var array массив передаваемых параметров
     */
    static public  $attr = [];

    /**
     * @var string (length = 2) текущий запрошеный язык
     */
    static public  $lang = '';

    static private $ajax;

    static public function isAjax(){

        if(self::$ajax !==  null){
            return self::$ajax;
        }

        if(
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return self::$ajax = true;
        } else {
            return self::$ajax = false;
        }
    }

    /**
     * @param $way array
     * @return array
     * @throws \Exception
     */
    static public function checkAllowedVariables($way){
        if (isset($way['options'])){
            $arr = self::$attr;
            foreach ($way['options'] as $option => $regV ){
                if (Is::regV(array_shift($arr), $regV) !== true){
                    throw new \Exception('Variable <strong>' . $option . '</strong> does not comply!');
                }
            }

        }
    }

}