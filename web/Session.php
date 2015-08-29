<?php
namespace liw\core\web;

use liw\core\Liw;

class Session
{
    static public function start()
    {
        session_name('liw');
        session_start();
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['login'])){
            Liw::$isGuest = false;
        }
    }

    static public function stop()
    {
        session_destroy();
    }


    static public function set($arr)
    {
        if(is_array($arr)){
            foreach($arr as $key => $value){
                $_SESSION[$key] = $value;
            }
            return;
        } else {
            throw new \Exception('Variable must be array, but <strong>' . gettype($arr) . '</strong> given.');
        }

    }

    static public function delete($variable)
    {
        if (isset($_SESSION[$variable])){
            unset ($_SESSION[$variable]);
        }
        return;
    }
}