<?php
namespace liw\core;

use liw\core\web\Request;

class Router
{
    static private $languages = ['ru', 'en', 'ua'];

    static public $way;

    static public function parseRequest($request, $ways)
    {
        $route = $request;
        $arr = explode('/', $request);
        $attr = [];
        $options = isset($ways['options']) ? $ways['options'] : [];
        while($route){
            if(isset($ways[$route])){
                if(count($attr) >= count($options)){
                    Request::$route = $route;
                    Request::$attr = $attr;
                    self::$way = $ways[$route];
                    return;
                } else {
                    throw new \Exception("Переданный массив данных меньше необходимого.");
                }
            } else {
                $last = array_pop($arr);
                if(strlen($last) == 2 && in_array($last, self::$languages)){
                    Request::$lang = $last;
                } else {
                    array_push($attr, $last);
                }
                $route = implode('/', $arr);
            }
        }
        throw new \Exception("No route: " . $request);
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