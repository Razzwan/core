<?php
namespace liw\core;

class ErrorHandler
{
    private $errors = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED',
    ];

    public function register()
    {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL | E_STRICT);
        set_error_handler([$this, 'showError']);
        register_shutdown_function([$this, 'catch_fatal_error']);
        ob_start();
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $file
     * @param int $line
     * @throws \Exception
     */
    public function showError($errno, $errstr, $file, $line)
    {
        $message = '<b>' . $this->errors[$errno] . "</b> [$errno]<hr>" . $errstr . '<hr> file: ' . $file . '<hr> line: ' . $line . '<hr>';
        self::insertErrorInLogs($errno, $errstr, $file, $line);
        $view = (new View())->getView();
        if (!defined('DEVELOP') || !DEVELOP){
            $view->showError(Lang::uage('error_404'));
        } else {
            $view->showError($message);
        }
        exit;
    }

    public function catch_fatal_error()
    {
        if ($error = error_get_last() AND $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            ob_end_clean();// сбросить буфер, завершить работу буфера
            // - вернуть заголовок 500
            header("HTTP/1.0 500");
            $this->showError($error['type'], $error['message'], $error['file'], $error['line']);
        } else {
            ob_end_flush();	// вывод буфера, завершить работу буфера
        }

    }

    private function insertErrorInLogs($errno, $errstr, $file, $line)
    {
        $file = LIW_WEB . "logs/errors.log";
        $lines = file($file);
        $lines[sizeof($lines)] = date("d.m.Y H:i:s ") . "Error: " . $this->errors[$errno] . " [$errno]. " . $errstr . '. File: ' . $file . '. Line: ' . $line . ".\n";
        file_put_contents($file, implode("", $lines));
    }

}
