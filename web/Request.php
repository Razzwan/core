<?php
/**
 * Created by Razzwan.
 * Date: 25.08.15
 * Time: 20:33
 */
namespace liw\core\web;

use liw\core\Liw;
use liw\core\validation\Clean;

class Request
{
    /**
     * Массив доступных в приложении языков (указывается в конфиге,
     *  соответствует физическому наличии одноименному файлу .php в app/config/languages
     * @var array
     */
    static private $languages;

    /**
     * @var string основной маршрут, полученный из uri
     */
    static public  $route;

    /**
     * @var array массив переданных скрипту параметров
     */
    static public $attr;

    /**
     * @var string (length = 2) текущий запрошеный язык, вырезается из $route,
     * если совпадает с одним из разрешенных языков в $languages
     */
    static public  $lang;

    /**
     * Хранит информацио о виде запроса: ajax/не ajax
     * @var boolean
     */
    static private $ajax;

    /**
     * Возвращает информацию о виде запроса: ajax/не ajax
     * @return bool
     */
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

    static public function getRequest($request = null)
    {
        /**
         * Вытаскиваем массив разрешенных языков из конфига
         */
        self::$languages = Liw::$config['languages'];

        /**
         * отделяем все до знака "?" и/или "#" и помещаем в переменную self::$route
         */
        if ($request ===  null) $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        self::$route = urldecode(Clean::url($request));

        self::getLang();
    }

    static private function getLang()
    {
        /**
         * если есть переменная из 2х символов, то считаем это языком и сохраняем
         */
        if (self::$route !== '/') {
            foreach(explode('/', trim(self::$route, '/')) as $language){
                if(strlen($language)==2 && in_array($language, self::$languages)){
                    self::$route = str_replace('/'.$language, '', self::$route); //вырезаем из route язык, чтоб не мешался
                    self::$lang = $language;
                    if (self::$route == '') self::$route = '/';
                }
            }
        }
    }

}
