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
}