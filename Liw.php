<?php
namespace liw\core;

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
