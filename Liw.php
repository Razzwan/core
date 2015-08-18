<?php
namespace liw\core;

/**
 * Файл инициализации приложения.
 */

if(defined('DEVELOP') && DEVELOP === true){
    /**
     * @const timestamp , Константа начала выполнения скрипта
     */
    defined("TIME") or define("TIME", microtime(true));

    /**
     * В режиме отладки должны отображаться все ошибки
     */
    error_reporting (E_ALL);
    ini_set('display_errors', 1);
}

/**
 * @const string PATH     корень liw каталога
 */
defined("LIW_CORE") or define("LIW_CORE", dirname(__DIR__) . DIRECTORY_SEPARATOR );

/**
 * Class Liw
 * @package liw\core
 * Статичный класс, в котором хранятся суперглобальные переменные (пока)
 */
class Liw
{
    static public $config = [];

    static public $lang   = [];

    static public $user = ['login'=>false];

}

require LIW_CORE . 'core/Loader.php'; //Подключение файла автозагрузки

/**
 * Регистрация автозагрузчика
 */
(new Loader())->register();
