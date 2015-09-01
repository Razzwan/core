<?php
namespace liw\core;

use liw\core\access\AccessDefault;
use liw\core\web\Request;
use liw\core\web\RequestNew;
use liw\core\web\Session;
use liw\core\validation\Clean;

class App
{
    /**
     * @param null|string $lang
     * @return void
     */
    static private function loadLanguage($lang = null)
    {
        if($lang !== null){
            $file = LIW_WEB . 'config/languages/' . $lang . '.php';
            if(file_exists($file)){
                Liw::$lang = require $file;
                return;
            }
        }

        if(!empty($_SESSION['language'])){
            $file = LIW_WEB . 'config/languages/' . $_SESSION['language'] . '.php';
            if(file_exists($file)){
                Liw::$lang = require $file;
                return;
            }
        }
        if(isset($_SESSION['language'])) unset($_SESSION['language']);
        $file = LIW_WEB . 'config/languages/' . Liw::$config['def_lang'] . '.php';
        Liw::$lang = require $file;
    }

    /**
     *
     */
    static public function start(){
        set_error_handler("self::show_errors"); // изменение отображения ошибок по умолчанию
        Liw::$config = require_once LIW_WEB . 'config/config.php';
        try {

            RequestNew::getRequest();



            Session::start();

            self::loadLanguage(Request::$lang);

            $access = new AccessDefault();

            if($access === null){
                $ways = include LIW_WEB . 'config/ways/guest.php';
            } else {
                $ways = $access::getWays();
            }

            if(!isset($ways[RequestNew::$route])){
                throw new \Exception('no route: '. RequestNew::$route);
            }

            $way = $ways[RequestNew::$route];
            RequestNew::checkAllowedVariables($way);

            self::mvc($way['controller'], $way['action']);
        }
        catch (\Exception $e) {
            self::show_errors($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    /**
     * Реализация архитектуры MVC
     * Метод создает контроллер, который соответствует текущему пути и запускает его метод, который так же соответствует
     * текущему пути. В этот метод передаются параметры из массива GET
     * @param string $controller
     * @param string $action
     * @throws \Exception
     */
    static private function mvc ($controller, $action) {
        $controller_route = '\web\controllers\\' . ucfirst($controller) . 'Controller';
        if (!class_exists($controller_route)) {
            throw new \Exception(Liw::$lang['message']['no_controller'] . $controller);
        }
        $controller_obj = new $controller_route();
        if (!method_exists($controller_obj, $action . 'Action')) {
            throw new \Exception(Liw::$lang['message']['no_action'] .
                '<strong>' . $action . '</strong> in controller <strong>' .
                $controller . '</strong>');
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
        call_user_func_array([$controller_obj, $action . 'Action'], Request::$attr);
        /**
         * Если существует метод after, то запускаем его после действия
         */
        if(method_exists($controller_obj, "after")){
            call_user_func_array([$controller_obj, "after"], Request::$attr);
        }
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $file
     * @param int $line
     * @throws \Exception
     */
    static public function show_errors($errno, $errstr, $file, $line)
    {
        $message = 'Error level: ' . $errno . '<hr>' . $errstr . '<hr>' . $file . '<hr>string: ' . $line . '<hr>';
        $view = View::getView();
        if (!defined('DEVELOP') || !DEVELOP){
            //добавить логирование
            $view->render(Liw::$config['def_route'], 'error', [
                'error' => Liw::$lang['message']['error']
            ]);
        } else {
            $view->render(Liw::$config['def_route'], 'error', [
                'error' => $message
            ]);
        }
        exit;
    }
}