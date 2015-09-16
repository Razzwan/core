<?php
namespace liw\core;

use liw\core\access\AccessMulti;
use liw\core\web\Request;
use liw\core\web\Session;

class App
{

    protected static $router;

    /**
     * Загружаем язык
     * @param null|string $lang
     * @throws \Exception
     */
    static private function loadLanguage($lang = null)
    {
        if($lang !== null){
            $file = LIW_WEB . 'config/languages/' . $lang . '.php';
            if(file_exists($file)){
                $_SESSION['language'] = $lang;
                Liw::$lang = require $file;
                return;
            } else {
                throw new \Exception("File " . $file . " not exist.");
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
     * Загружаем все необходимые данные и запускаем выполнение метода контроллера
     */
    static public function start(){
        set_error_handler("self::show_errors"); // изменение отображения ошибок по умолчанию
        Liw::$config = require_once LIW_WEB . 'config/config.php';
        try {

            Session::start();

            Request::getRequest();

            self::loadLanguage(Request::$lang);

            self::run(new Router(include LIW_WEB . "config/ways/all.php"));
        }
        catch (\Exception $e) {
            self::show_errors($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
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
            $view->render('main', 'error', [
                'error' => Liw::$lang['message']['error']
            ]);
        } else {
            $view->render('main', 'error', [
                'error' => $message
            ]);
        }
        exit;
    }

    static public function run($router)
    {
        $router = $router->run();

        $controller_route = '\web\controllers\\' . $router['action'][0] . 'Controller';
        if (!class_exists($controller_route)) {
            throw new \Exception(Liw::$lang['message']['no_controller'] . $router['action'][0] . 'Controller');
        }
        $controller_obj = new $controller_route();
        if (!method_exists($controller_obj, $router['action'][1] . 'Action')) {
            throw new \Exception(Liw::$lang['message']['no_action'] .
                '<strong>' . $router['action'][1] . 'Action' . '</strong> in controller <strong>' .
                $router['action'][0] . 'Controller' . '</strong>');
        }

        /**
         * Если существует метод before, то запускаем его перед действием
         */
        if (method_exists($controller_obj, "before")) {
            call_user_func_array([$controller_obj, "before"], Request::$attr);
        }
        /**
         * запускает метод контроллера с параметрами
         */
        call_user_func_array([$controller_obj, $router['action'][1] . 'Action'], Request::$attr);
        /**
         * Если существует метод after, то запускаем его после действия
         */
        if (method_exists($controller_obj, "after")) {
            call_user_func_array([$controller_obj, "after"], Request::$attr);
        }
    }
}
