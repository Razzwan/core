<?php
namespace liw\core;

class Controller
{
    /**
     * Действие по умолчанию
     * @var string
     */
    public $default_action = 'index';

    public function beforeAction(){}

    /**
     * Запускает генерацию страницы
     * @param $view
     * @param null $attributes
     * @throws \Exception
     */
    public function render($view, $attributes = null)
    {
        $this->beforeAction();
        View::getView()->render($this->getClassFromPath(), $view, $attributes);
        $this->afterAction();
    }

    /**
     * осуществляет перенаправление
     * @param $action
     * @param null $attr
     * @throws \Exception
     */
    public function redirect($action, $attr = null){
        if(is_array($action)){
            $this->beforeAction();
            View::getView()->render($action[0],$action[1], $attr);
            $this->afterAction();
            return;
        }
        header('Location: '. Liw::$config['domain'] . $action);
    }

    public function afterAction(){}


    /**
     * Возвращает название класса, бъект которого создается
     * в нижнем регистре
     * из строки "\liw\controllers\MainController" делает "main"
     *
     * @return string нижний регистр
     */
    private function getClassFromPath()
    {
        $path = get_class($this);
        $folder = str_replace('controller', '', substr(strrchr(strtolower($path), "\\"), 1));
        return $folder;
    }

}
