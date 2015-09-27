<?php
namespace liw\core;

use liw\core\web\Request;

class Controller
{
    /**
     * Название вызвавшего класса в нижнем регистре (например 'main' для класса liw\web\MainController)
     * @var $folder string
     */
    private $called_class;

    /**
     * 1. Присваивает название класса, бъект которого создается переменной $folder
     * в нижнем регистре
     * из строки "\liw\controllers\MainController" делает "main"
     * 2. Загружает язык, если есть такой файл в папке languages
     */
    public function __construct()
    {
        $path = get_class($this);
        $this->called_class = str_replace('controller', '', substr(strrchr(strtolower($path), "\\"), 1));
        /**
         * Загружаем файл языка и добавляем его к переменной Lang::uage()
         */
        $file_lang = LIW_LANG . Request::$lang . '/' . $this->called_class . '.php';
        if(file_exists($file_lang)){
            Lang::add(require $file_lang);
        }

    }

    /**
     * Запускает генерацию страницы
     * @param $view
     * @param null $attributes
     * @throws \Exception
     */
    public function render($view, $attributes = null)
    {
        if(is_object($attributes)){
            $attributes = $attributes->fields;
        }

        try{
            (new View)->getView(isset($this->layout)?$this->layout:null)->render($this->called_class, $view, $attributes);
        } catch(\Exception $e){
            (new ErrorHandler())->showError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        }

    }

    /**
     * осуществляет перенаправление
     * @param $action
     * @param null $attr
     * @throws \Exception
     */
    public function redirect($action, $attr = null){

        if(is_object($attr)){
            $attr = $attr->fields;
        }

        if(is_array($action)){
            (new View)->getView()->render($action[0],$action[1], $attr);
            return;
        }
        header('Location: ' . $action);
    }

    public function twig($view, $attr = [])
    {
        (new View)->getView(isset($this->layout)?$this->layout:null)->twig($this->called_class, $view, $attr);
    }

}
