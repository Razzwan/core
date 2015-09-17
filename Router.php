<?php
namespace liw\core;

use liw\core\web\Request;
use liw\core\validation\Is;

/**
 * Class Router
 * @package liw\core
 * Принимает обработанный маршрут $route и массив разрешенных роутов.
 * Возвращает выполняемый контроллер и метод, массив передаваемых в них параметров
 */
class Router
{
    /**
     * Массив вида ['ActiveController', 'activeAction', 'r'=>['option1' => 'regV1', 'option2' => 'regV2', ...]]
     * @var array
     */
    static public $way;

    static private $rv = [
        'd' => '#^[0-9]+$#'
    ];

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

    static private function getRegFromRoute()
    {
        $arr = explode('/', $route);
        $fragment = array_shift($arr);
        while($fragment){
            $length = mb_strlen($fragment, "UTF-8");
            strncasecmp ($fragment, $ways[0], $length);
            $fragment = array_shift($arr);
        }
    }

    static public function createRegV($route) //strncasecmp
    {
        $arr = explode('/', $route);
        for ($i=0; $i<count($arr); $i++){
            if ($arr[$i][0] === "{"){
                if($arr[$i][1] === ":"){
                    $arr[$i] = self::$rv[mb_substr($arr[$i], 2, mb_strlen($arr[$i]) - 3)];
                } else {
                    $arr[$i] = mb_substr($arr[$i], 1, mb_strlen($arr[$i]) - 2);
                }
            }
        }
        return implode($arr);
    }

    static public function run(){
        $way_arr = explode(':', self::$way[0]);
        $controller_route = '\web\controllers\\' . $way_arr[0] . 'Controller';
        if (!class_exists($controller_route)) {
            throw new \Exception(Liw::$lang['message']['no_controller'] . $way_arr[0] . 'Controller');
        }
        $controller_obj = new $controller_route();
        if (!method_exists($controller_obj, $way_arr[1] . 'Action')) {
            throw new \Exception(Liw::$lang['message']['no_action'] .
                '<strong>' . $way_arr[1] . 'Action' . '</strong> in controller <strong>' .
                $way_arr[0] . 'Controller' . '</strong>');
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
        call_user_func_array([$controller_obj, $way_arr[1] . 'Action'], Request::$attr);
        /**
         * Если существует метод after, то запускаем его после действия
         */
        if(method_exists($controller_obj, "after")){
            call_user_func_array([$controller_obj, "after"], Request::$attr);
        }
    }
}
