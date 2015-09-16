<?php
namespace liw\core;

use liw\core\web\Request;

class Router
{
    private $rules = []; //Правила для роутера
    private $url = []; //Адресная строка
    private $action = []; //Контроллер и метод
    private $result = [];

    /**
     * Добавление правил для роутинга
     *
     * @param array $rules
     */
    public function __construct($rule)
    {
        foreach ($rule as $url => $param) {
            $this->rules[$url] = $param;
        }
    }

    /**
     * @return string
     */
    private function breakUrl()
    {
        $this->url = $_SERVER['REQUEST_URI'];
    }

    /**
     * Провека на совпадение адресной c справилом
     */
    private function parseURL()
    {
        $this->url = urldecode($this->url);

        foreach ($this->rules as $url => $param) {

            if (preg_match("#^{$url}$#ui", $this->url, $match)) {
                array_shift($match);
                Request::$attr = $match;
                $this->action = $param;
                return $this->action = $param;
            }

        }


    }

    /**
     * Разбиваем сторку с названием контроллера и метода
     *
     * @param $action
     */
    private function parseAction()
    {
        $this->result['action'] = explode("::", $this->action['action']);
        $this->result['param'] = explode("/", trim($this->url, '/'));
    }

    /**
     * Проверяем метод запроса на совпадение с правилом
     *
     * @return bool
     */
    private function getMethod()
    {
        if (strtoupper($this->action['method']) == $_SERVER['REQUEST_METHOD']) {
            return true;
        }
    }

    /**
     * Запус роутера
     *
     * @return bool
     */
    public function run()
    {
        /**
         * проверка существуют ли правила
         */
        if (empty($this->rules)) {
            return false;
        }

        /**
         * Получаем массив адресной строки
         */
        $this->breakUrl();

        /**
         * Проверяем url с правилами
         */
        if (!is_array($this->parseURL())) {
            return false;
        }

        /**
         * Проверяем метод запроса
         */
        if (!$this->getMethod()) {
            return false;
        }

        /**
         * Узнаем контроллер и метод
         */
        $this->parseAction();

        return $this->result;
    }
}

