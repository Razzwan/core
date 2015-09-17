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

    /**
     * @var array позиции переменных в строке
     */
    static private $var_positions = [];

    /**
     * @var array массив параметров, переданных в урл через слеши и указанные в ключе массива $ways
     */
    static public $attr = [];

    /**
     * @var array регулярные выражения доступные из коробки. Доступны по ключю
     */
    static private $reg_exps = [
        'i' => "[0-9]+",
        's' => "\w+",
        'd' => "[0-9\.]+",
    ];

    /**
     * @param $route
     * @param $ways
     * @return array
     * @throws \Exception
     */
    static public function getWay($route, $ways)
    {
        foreach(array_keys($ways) as $way){
            if(mb_ereg_match(self::createRegV($way), $route)){
                /**
                 * Возвращаем название контроллера и метода в нем
                 */
                self::$way = $ways[$way];

                if(count(self::$way)>=2){
                    if(!array_key_exists('method', self::$way)){
                        self::$way['method'] = self::$way[1];
                    }
                }
                if(isset(self::$way['method']) && !self::checkMethod(self::$way['method'])){
                    throw new \Exception('Методе передачи данных (GET, POST...) не соответствет указанному в файле роутинга.');
                }
                if(!array_key_exists('action', self::$way)){
                    if(isset(self::$way[0])){
                        self::$way['action'] = self::$way[0];
                    } else {
                        throw new \Exception('Ошибка синтаксиса в фале роутинга.');
                    }
                }
                /**
                 * Возвращаем массив, ключи которого
                 */
                Request::$attr = array_merge(self::$attr = array_intersect_key(explode('/', $route), self::$var_positions), Request::$attr);
                return self::$way;

            }
        }
        throw new \Exception("Нет такого маршрута: " . $route);
    }

    /**
     * Проверяем соответствует ли указанный в маршрутизации метод реальному методу передаваемых данных
     * @param $method
     * @return bool
     */
    static private function checkMethod($method)
    {
        if (is_array($method)){
            if(in_array($_SERVER['REQUEST_METHOD'], $method)){
                return true;
            }
        } else {
            if(strtoupper($method) === $_SERVER['REQUEST_METHOD']){
                return true;
            }
        }
        return false;
    }

    static public function createRegV($route) //strncasecmp
    {
        $arr = explode('/', $route);
        for ($i=0; $i<count($arr); $i++){
            if (mb_substr($arr[$i], 0, 1, "UTF-8") === "{"){
                self::$var_positions[$i] = $i;
                if(mb_substr($arr[$i], 1, 1, "UTF-8") === ":"){
                    $arr[$i] = self::$reg_exps[mb_substr($arr[$i], 2, 1, "UTF-8")];
                } else {
                    $arr[$i] = mb_substr($arr[$i], 1, mb_strlen($arr[$i], "UTF-8") - 2, "UTF-8");
                }
            }
        }
        return "^" .implode('/', $arr) . "$";
    }

    static public function run(){
        $way_arr = explode(':', self::$way[0]);
        $controller_route = '\web\controllers\\' . $way_arr[0] . 'Controller';
        if (!class_exists($controller_route)) {
            throw new \Exception(Lang::$uage['error_no_controller'] . $way_arr[0] . 'Controller');
        }
        $controller_obj = new $controller_route();
        if (!method_exists($controller_obj, $way_arr[1] . 'Action')) {
            throw new \Exception(Lang::$uage['error_no_action'] .
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
