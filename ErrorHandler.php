<?php
namespace liw\core;

class ErrorHandler
{
    static public $errors = [];

    static public function init()
    {
        set_error_handler(['ErrorHandler', 'show_errors']);
        //register_shutdown_function('ErrorHandler::catch_fatal_error');
        //ob_start();
    }

    static public function addError($error)
    {
        self::$errors[] = $error;
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
        ob_end_clean();
        echo 'in show errors ErrorHandler';
        exit;
        $message = 'Error level: ' . $errno . '<hr>' . $errstr . '<hr>' . $file . '<hr>string: ' . $line . '<hr>';
        $view = View::getView();
        if (!defined('DEVELOP') || !DEVELOP){
            //добавить логирование
            $view->render('main', 'error', [
                'error' => Lang::uage('error_404')
            ]);
        } else {
            $view->showError($message);
        }
        exit;
    }

    static public function catch_fatal_error()
    {
        ob_end_clean();
        echo 'adfasdf';
        exit;
        $error = error_get_last();
        if (isset($error))
            if($error['type'] == E_ERROR
                || $error['type'] == E_PARSE
                || $error['type'] == E_COMPILE_ERROR
                || $error['type'] == E_CORE_ERROR)
            {
                ob_end_clean();// сбросить буфер, завершить работу буфера

                var_dump($error);
                exit;

                // контроль критических ошибок:
                // - записать в лог
                self::insertErrorInLogs($error);
                // - вернуть заголовок 500
                header("HTTP/1.0 500");
                // - вернуть после заголовка данные для пользователя
            }
            else
            {
                ob_end_flush();	// вывод буфера, завершить работу буфера
            }
        else
        {
            ob_end_flush();	// вывод буфера, завершить работу буфера
        }

    }

    static private function insertErrorInLogs($error)
    {
        $file = LIW_WEB . "logs/errors.log";
        $lines = file($file);
        $lines[sizeof($lines)-1] = date("d m Y H:i:s") . "Error: " . implode(" ", $error) . "\n";
        file_put_contents($file, implode("", $lines));
    }

}