<?php
namespace liw\core\routers;

use liw\core\Liw;
use liw\core\validation\Clean;
use liw\core\web\Request;

/**
 * Класс отвечает за маршрутизацию
 * Class SmartRouter
 * @package liw\core
 */
class SmartRouter
{
    /**
     * Массив всех доступным маршрутов
     * @var array
     */
    static public $ways = [];

    /**
     * Текущий путь без параметров
     * @var string
     */
    static private $route;

    /**
     * Получает массив всех доступных маршрутов, исходя из прав доступа пользователя
     * @return mixed
     * @throws \Exception
     */
    static public function getRoute(){
        if(Liw::$isGuest){
            self::$ways = include LIW_WEB . "config/ways/guest.php";
        }else{
            self::$ways = include LIW_WEB . "config/ways/login.php";
            if(!empty($_SESSION['user']['levels'])){
                self::$ways = array_merge(self::$ways, self::filesFromLevels());
            }
        }

        $arr = explode('?', Clean::url($_SERVER['REQUEST_URI']));
        self::$route = Request::$url = array_shift($arr);

        foreach(explode('/', self::$route) as $str){
            if(strlen($str)==2){
                $language = $str;
                self::$route = str_replace('/'.$language, '', self::$route);
                break;
            }
        }

        if (isset(self::$ways[self::$route])) {
            self::$ways = self::$ways[self::$route];
            if(!empty($_GET)){
                self::$ways['attributes'] = $_GET;
            }
            if(!empty($language)){
                self::$ways['language'] = $language;
            }
            return self::$ways;
        }
        return false;
    }

    /**
     * Загружает файлы маршрутов, в зависимости от переменной levels
     * @return array
     */
    static private function filesFromLevels(){
        /*$str = $_SESSION['user']['levels'];
        Liw::$user['levels']['article'] = substr($str, 0, 1);*/
        $levels = preg_split('//u', $_SESSION['user']['levels'], -1,PREG_SPLIT_NO_EMPTY);
        Liw::$user['levels'] = $levels;
        $arr = [];
        for ($i=1; $i<=$levels[0]; $i++){
            $add_arr = include LIW_WEB ."config/ways/article/" . $i .".php";
            $arr = array_merge($arr, $add_arr);
        }
        return $arr;
    }

}