<?php
namespace liw\core;

use liw\core\access\AccessDefault;
use liw\core\access\AccessMulti;
use liw\core\web\Request;
use liw\core\web\Session;

class App
{
    /**
     * Загружаем язык
     * @param null|string $lang
     * @throws \Exception
     */
    static private function checkLanguage($lang = null)
    {
        if($lang !== null){
            $file = LIW_WEB . 'config/languages/' . $lang . '/' . $lang . '.php';
            if(file_exists($file)){
                $_SESSION['language'] = $lang;
                Lang::add(require $file);
                return;
            } else {
                throw new \Exception("Файл " . $file . " не существует.");
            }
        }

        if(!empty($_SESSION['language'])){
            $lang = $_SESSION['language'];
            $file = LIW_WEB . 'config/languages/' . $lang . '/' . $lang . '.php';
            if(file_exists($file)){
                Lang::add(require $file);
                return;
            }
        }
        if(isset($_SESSION['language'])) unset($_SESSION['language']);
        $lang = Liw::$config['def_lang'];
        $file = LIW_WEB . 'config/languages/' . $lang . '/' . $lang . '.php';
        Lang::add(require $file);
    }

    /**
     * Загружаем все необходимые данные и запускаем выполнение метода контроллера
     */
    static public function start(){
        Liw::$config = require_once LIW_WEB . 'config/config.php';

        (new ErrorHandler)->register();

        try {
            fasdfasdf();
            Session::start();

            Request::getRequest();

            self::checkLanguage(Request::$lang);

            Router::getWay(Request::$route, AccessMulti::getWays());

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
        $view = (new View)->getView();
        if (!defined('DEVELOP') || !DEVELOP){
            //добавить логирование
            $view->render('main', 'error', [
                'error' => Lang::uage('error_404')
            ]);
        } else {
            $view->render('main', 'error', [
                'error' => $message
            ]);
        }
        exit;
    }
}
