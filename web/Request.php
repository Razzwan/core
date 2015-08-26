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
    static public  $url;
    static public  $attr;
    static public  $get;
    static public  $lang;

    static public function getRequest()
    {
        /**
         * отделяем все до знака ? и помещаем в переменную url
         */
        $arr = explode('?', Clean::url($_SERVER['REQUEST_URI']));
        self::$url = array_shift($arr);
        /**
         * если есть переменная из 2х символов, то считаем это языком и сохраняем
         */
        if (!empty(self::$url)) {
            foreach(explode('/', self::$url) as $language){
                if(strlen($language)==2){
                    self::$url = str_replace('/'.$language, '', Request::$url); //вырезаем из url язык, чтоб не мешался
                    self::$lang = $language;
                }
            }
        }
        /**
         * символом '/:' отделены переменные
         */
        $arr = explode('/:', self::$url);
        self::$url = array_shift($arr);                  // отрезали и сохранили основную часть url
        self::$attr = count($arr) ? $arr : null;
        if(!empty($_GET)){
            self::$get = &$_GET;                         //отдельно сохранили массив GET
        }
    }

}