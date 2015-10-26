<?php
namespace liw\core;

use liw\core\access\AccessDefault;
use liw\core\access\AccessMulti;
use liw\core\web\Request;
use liw\core\web\Session;

class App
{
    /**
     * Загружаем все необходимые данные и запускаем выполнение метода контроллера
     */
    static public function start(){

        Liw::$config = require_once LIW_WEB . 'config/config.php';

        ErrorHandler::register();

        try {

            Session::start();

            Request::getRequest();

            Lang::checkLanguage(Request::$lang);

            Router::getWay(Request::$route, AccessMulti::getWays());

            Router::run();

        }
        catch (\Exception $e) {

            ErrorHandler::showError("Exception", $e->getMessage(), $e->getFile(), $e->getLine());

        }
    }
}
