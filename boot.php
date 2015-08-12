<?php
/**
 *Фаил инициализации приложения
 */
namespace liw\core;

/**
 * Определяем с какими параметрами и какие модули загружаются, в зависимости от константы DEVELOP
 */
use liw\Autoload;

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
define('PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR );

require PATH . '/core/Liw.php';

require PATH . '/core/Autoload.php'; //Подключение файла автозагрузки

/**
 * Регистрация автозагрузчика
 */
Autoload::register();
