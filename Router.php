<?php
namespace liw\core;

use liw\core\web\Request;

class Router
{
    private $languages = ['ru', 'en', 'ua'];

    public function parseRequest($request, $ways)
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
                } else {
                    throw new \Exception("Размерность запрашиваемого массива не совпада");
                }
                return;
            } else {
                $last = array_pop($arr);
                if(strlen($last) == 2 && in_array($last, $this->languages)){
                    Request::$lang = $last;
                } else {
                    array_push($attr, $last);
                    $route = implode($arr);
                }
            }
        }
        throw new \Exception("No route " . $request);
    }
}