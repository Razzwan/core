<?php
namespace liw\core;

/**
 * Файл инициализации приложения.
 */

/**
 * @const string PATH     корень liw каталога
 */
defined("LIW_CORE") or define("LIW_CORE", dirname(__DIR__) . DIRECTORY_SEPARATOR );

/**
 * Проверяем, установлен ли флаг среды разработки, если ды - выставляем соотв. настройки
 */
if(defined('DEVELOP') && DEVELOP === true){
    /**
     * В режиме отладки должны отображаться все ошибки
     */
    error_reporting (E_ALL);
    ini_set('display_errors', 1);
    Lang::add(require_once LIW_CORE . 'core/develop/lang/lang.php');
}

/**
 * Class Liw
 * @package liw\core
 * Статичный класс, в котором хранятся суперглобальные переменные (пока)
 */
class Liw
{
    static public $config = [];

    static public $isGuest = true;

    static public $user = [];

}

require LIW_CORE . 'core/Loader.php'; //Подключение файла автозагрузки

/**
 * Регистрация автозагрузчика
 */
(new Loader())->register();

