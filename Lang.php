<?php
namespace liw\core;

class Lang
{
    static private $_ = [];

    static public function add($arr)
    {
        self::$_ = array_merge($arr, self::$_);
    }

    static public function uage($_)
    {
        if(isset(self::$_[$_])){
            return self::$_[$_];
        }
        /**
         * закомментировать следующую линию, если не нужно автозаполнение файлов
         */
        //self::insertLine($_);
        return '?' . $_ . '?';
    }

    static private function insertLine($field)
    {
        if(defined("DEVELOP") && DEVELOP === true){
            $file = LIW_CORE . "core/develop/lang/lang.php";
            $lines = file($file);
            $lines[sizeof($lines)-1] = "    '{$field}' => '$field',\n];";
            file_put_contents($file, implode("", $lines));
        }
    }

    /**
     * Загружаем язык
     * @param null|string $lang
     * @throws \Exception
     */
    static public function checkLanguage($lang = null)
    {
        if($lang !== null){
            $file = LIW_WEB . 'config/languages/' . $lang . '/' . $lang . '.php';
            if(file_exists($file)){
                $_SESSION['language'] = $lang;
                Lang::add(require $file);
                return;
            } else {
                throw new \Exception("Файл " . $file . " не существует.");
            }
        }

        if(!empty($_SESSION['language'])){
            $lang = $_SESSION['language'];
            $file = LIW_WEB . 'config/languages/' . $lang . '/' . $lang . '.php';
            if(file_exists($file)){
                Lang::add(require $file);
                return;
            }
        }
        if(isset($_SESSION['language'])) unset($_SESSION['language']);
        $lang = Liw::$config['def_lang'];
        $file = LIW_WEB . 'config/languages/' . $lang . '/' . $lang . '.php';
        Lang::add(require $file);
    }

}
