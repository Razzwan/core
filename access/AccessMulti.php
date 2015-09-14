<?php
namespace liw\core\access;

use liw\core\Liw;

class AccessMulti implements AccessInterface
{
    /**
     * Массив всех имен для разрешений
     * @var array
     */
    static private $access_names = [
        'articles'
    ];

    /**
     * Возвращает все разрешенные для данного пользователя маршруты
     * Нужно добавить сохранение их в файл кэша
     * @return array|mixed
     * @throws \Exception
     */
    static public function getWays()
    {
        if(Liw::$isGuest){
            $file = LIW_WEB . 'config/ways/guest.php';
            return self::loadFile($file);
        } else {
            $file = LIW_WEB . 'config/ways/login.php';
            return array_merge(self::loadFile($file), self::filesFromLevels());
        }

    }

    static private function loadFile($file)
    {
        if(is_file($file)){
            return require_once $file;
        } else {
            throw new \Exception("File: " . $file . " not exist.");
        }
    }

    /**
     * Загружает файлы маршрутов, в зависимости от переменной levels
     * @return array
     */
    static private function filesFromLevels(){

        if (isset($_SESSION['user']['levels']) && $_SESSION['user']['levels']){

            $str = $_SESSION['user']['levels'];
            $levels = explode('.', $str);
            $arr = [];

            for($i=0; $i<count($levels); $i++){
                Liw::$user['level'][self::$access_names[$i]] = $levels[$i];
                for ($j=1; $j<=$levels[$i]; $j++){
                    $file = LIW_WEB ."config/ways/" . self::$access_names[$i] . "/" . $j .".php";
                    $add_arr = self::loadFile($file);
                    $arr = array_merge($arr, $add_arr);
                }
            }
            return $arr;
        }

    }
}
