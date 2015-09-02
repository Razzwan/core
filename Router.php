<?php
namespace liw\core;

use liw\core\web\Request;
use liw\core\validation\Is;

class Router
{
    /**
     * Массив вида ['ActiveController', 'activeAction', 'r'=>['option1' => 'regV1', 'option2' => 'regV2', ...]]
     * @var array
     */
    static public $way;

    static public function getWay($route, $ways){
        if (isset($ways[$route])){
            self::$way = $ways[$route];
            if (isset($way['r'])){
                $attr = Request::$attr;
                if(count($ways['r']) > count($attr)){
                    throw new \Exception("Ожидаемое количество переменных больше переданного.");
                }
                foreach ($way['r'] as $option => $regV ){
                    if (Is::regV(array_shift($attr), $regV) !== true){
                        throw new \Exception('Variable <strong>' . $option . '</strong> does not comply!');
                    }
                }
            }
            return;
        } else {
            throw new \Exception("No route: " . $route);
        }
    }


    static public function run(){
        $controller_route = '\web\controllers\\' . self::$way[0];
        if (!class_exists($controller_route)) {
            throw new \Exception(Liw::$lang['message']['no_controller'] . self::$way[0]);
        }
        $controller_obj = new $controller_route();
        if (!method_exists($controller_obj, self::$way[1])) {
            throw new \Exception(Liw::$lang['message']['no_action'] .
                '<strong>' . self::$way[1] . '</strong> in controller <strong>' .
                self::$way[0] . '</strong>');
        }

        /**
         * Если существует метод before, то запускаем его перед действием
         */
        if(method_exists($controller_obj, "before")){
            call_user_func_array([$controller_obj, "before"], Request::$attr);
        }
        /**
         * запускает метод контроллера с параметрами
         */
        call_user_func_array([$controller_obj, self::$way[1]], Request::$attr);
        /**
         * Если существует метод after, то запускаем его после действия
         */
        if(method_exists($controller_obj, "after")){
            call_user_func_array([$controller_obj, "after"], Request::$attr);
        }
    }
}