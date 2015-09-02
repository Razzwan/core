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
                $_SESSION['language'] = $lang;
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

            Session::start();

            Request::getRequest();

            self::loadLanguage(Request::$lang);

            Router::getWay(Request::$route, AccessDefault::getWays());

            Router::run();
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