<?php
namespace liw\core;

/**
 * Файл инициализации приложения.
 */

if(defined('DEVELOP') && DEVELOP === true){
    /**
     * @const timestamp , Константа начала выполнения скрипта
     */
    define("TIME", microtime(true));

    /**
     * В режиме отладки должны отображаться все ошибки
     */
    error_reporting (E_ALL);
    ini_set('display_errors', 1);
}

/**
 * @const string PATH     корень liw каталога
 */
define('LIW_CORE', dirname(__DIR__) . DIRECTORY_SEPARATOR );

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

    static public $dev = [
        'classes'  => [],
        'requests' => [],
    ];

}

require LIW_CORE . '/core/Psr4AutoloaderClass.php'; //Подключение файла автозагрузки

/**
 * Регистрация автозагрузчика
 */
(new Psr4AutoloaderClass)->register();
