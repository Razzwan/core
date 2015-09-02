<?php
/**
 * Created by Razzwan.
 * Date: 25.08.15
 * Time: 20:33
 */
namespace liw\core\web;

use liw\core\validation\Clean;

class Request
{
    static private $languages = ['ru', 'en', 'ua'];

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

    static public function getRequest($request = null)
    {
        /**
         * отделяем все до знака ? и помещаем в переменную url
         */
        if ($request ===  null) $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        self::$route = urldecode(Clean::url($request));

        self::getLang();

        self::getAttr();
    }

    static private function getLang()
    {
        /**
         * если есть переменная из 2х символов, то считаем это языком и сохраняем
         */
        if (self::$route !== '/') {
            foreach(explode('/', self::$route) as $language){
                if(strlen($language)==2 && in_array($language, self::$languages)){
                    self::$route = str_replace('/'.$language, '', self::$route); //вырезаем из route язык, чтоб не мешался
                    self::$lang = $language;
                }
            }
        }
    }

    static private function getAttr()
    {
        /**
         * символом '/:' отделены переменные
         */
        $arr = explode('/:', self::$route);
        self::$route = array_shift($arr);                  // отрезали и сохранили основную часть url
        self::$attr = count($arr) ? $arr : [];
        if(!empty($_GET)) {
            self::$attr = array_merge(self::$attr, $_GET); //если массив GET не пуст, то добавляем его элементы в конец
        }                                                    //массива Request::$attr
    }

}