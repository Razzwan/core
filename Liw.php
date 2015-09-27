<?php
namespace liw\core;

/**
 * Файл инициализации приложения.
 */

/**
 * @const string LIW_CORE     корень liw каталога
 * @const string LIW_LANG     папка с языками
 */
defined("LIW_CORE") or define("LIW_CORE", dirname(__DIR__) . DIRECTORY_SEPARATOR );
defined("LIW_LANG") or define("LIW_LANG", '/home/www/blog.loc/blog/config/languages/');

/**
 * Проверяем, установлен ли флаг среды разработки, если ды - выставляем соотв. настройки
 */
if(defined('DEVELOP') && DEVELOP === true){
    /**
     * В режиме отладки должны отображаться все ошибки
     */
    error_reporting (E_ALL);
    ini_set('display_errors', 1);

    /**
     * Подключаем файл с языковыми данными для разработчика
     */
    Lang::add(require LIW_LANG . 'dev.php');

    /**
     * Подключаем файл helpers.php
     */
    require_once LIW_CORE . 'core/develop/helpers.php';
}

/**
 * Class Liw
 * @package liw\core
 * Статичный класс, в котором хранятся суперглобальные переменные (пока)
 */
class Liw
{
    static private $levels = [];

    static public $config = [];

    static public $isGuest = true;

    static public $user = [];

    static public function level($var)
    {
        if(isset(self::$levels[$var])){
            return (int)self::$levels[$var];
        }
        return 0;
    }

    static public function setLevel($var, $value)
    {
        self::$levels[$var] = $value;
    }

}

require LIW_CORE . 'core/Loader.php'; //Подключение файла автозагрузки

/**
 * Регистрация автозагрузчика
 */
(new Loader())->register();

