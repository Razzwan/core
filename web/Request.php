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

    /**
     * @param null $request
     */
    static public function getRequest($request = null)
    {
        /**
         * отделяем все до знака ? и помещаем в переменную url
         */
        if ($request ===  null) $request = $_SERVER['REQUEST_URI'];
        $request = Clean::url($request);
        $arr = explode('?', $request);
        self::$route = array_shift($arr);

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
                if(strlen($language)==2){
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

    static public function isAjax(){
        if(
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        } else {
            return false;
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
                if (Is::valid('regV', array_shift($arr), $regV) !== true){
                    throw new \Exception('Variable <strong>' . $option . '</strong> does not comply!');
                }
            }

        }
    }

}