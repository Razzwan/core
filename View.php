<?php
namespace liw\core;

/**
 * Класс отображения
 * для доступа информации в видах используются следующие переменные:
 * 1. $this->title - текущее название страницы может быть задано в виде
 */
class View
{
    /**
     * Объект вида
     * @var object
     */
    private static $_view;

    /**
     * html-разметка текущего вида страницы
     * @var string
     */
    public $html = '';

    /**
     * путь, где находится текущий вид для отображения
     *
     * @var string
     */
    public $view_path = '';

    /**
     * @var string
     * путь к папке, где лежат виды
     */
    private $view_folder = '';

    /**
     * задается <title>
     *
     * @var string
     */
    public $title = '';

    /**
     * Массив значений, переданый в вид
     * @var array
     */
    public $attr = [];

    /**
     * Устанавливает название документа по умалчанию
     * устанавливает название папки, в каталоге \liw\views,
     * где хранятся виды для текущего контроллера
     *
     * @param $folder string название папки, где хранятся текущие виды
     */
    private function __construct()
    {
        $this->title = Liw::$config['site_name'];
    }

    /**
     * запрет клонирования
     */
    private function __clone(){}

    public static function getView()
    {
        if (null === self::$_view) {
            self::$_view = new self();
        }
        return self::$_view;
    }

    public function render($folder, $view, $attr = null)
    {
        ob_start();
        $this->view_path = LIW_WEB . 'views/' . $folder . '/' . $view . '.php';
        if(is_file($this->view_path)){
            $this->view_folder = $folder;
            if(isset($attr) && is_array($attr)){
                $this->attr = $attr;
                extract($attr, EXTR_OVERWRITE);
            }
            /**
             * Добавляем панель разработчика, если выставлен соответствующий флаг
             */
            if(defined('DEVELOP') && DEVELOP === true){
                include __DIR__ . '/base/view/develop_panel.php';
            }
            include $this->view_path;
        }else{
            throw new \Exception('File: ' . $this->view_path . ' not exist!');
        }
        $this->view =  ob_get_clean();
        require LIW_WEB . 'views/layouts/' . Liw::$config['def_layout'] . '.php'; //подключение layout
        exit;
    }

    public function showBlock($view, $attr = null)
    {
        $this->view_path = LIW_WEB . 'views/' . $this->view_folder . '/' . $view . '.php';
        if(is_file($this->view_path)){
            if(isset($attr) && is_array($attr)){
                extract($attr, EXTR_OVERWRITE);
            } elseif($this->attr){
                extract($this->attr, EXTR_OVERWRITE);
            }
            include $this->view_path;
        }else{
            ob_end_clean();
            throw new \Exception('File: ' . $this->view_path . ' not exist!');
        }
    }

    /**
     * @param $view
     * @param array $attr
     * @throws \Exception
     */
    /*public function show($view, $attr = null)
    {
        $this->showAttributes = $attr;
        if(ob_get_length()) ob_end_clean();
        ob_start();
        if(isset($attr) && is_array($attr)) {
            extract($attr, EXTR_OVERWRITE);
        }
        $this->view_path = LIW_WEB . 'views/' . $this->view_folder . '/' . $view . '.php';

        /**
         * Добавляем панель разработчика, если выставлен соответствующий флаг
         */
        /*if(defined('DEVELOP') && DEVELOP === true){
            include __DIR__ . '/base/view/develop_panel.php';
        }

        if(is_file($this->view_path)){
            include $this->view_path;
        }else{
            throw new \Exception('File: ' . $this->view_path . ' not exist!');
        }

        $this->view = ob_get_clean();
        require LIW_WEB . 'views/layouts/' . Liw::$config['def_layout'] . '.php'; //подключение layout
    }*/

    /**
     * @param $controller
     * @param $view
     * @param null $attr
     * @throws \Exception
     */
    /*public function redirect($controller, $view, $attr = null)
    {
        if(ob_get_length()) ob_end_clean();
        ob_start();

        /**
         * Добавляем панель разработчика, если выставлен соответствующий флаг
         */
        /*if(defined('DEVELOP') && DEVELOP === true){
            include __DIR__ . '/base/view/develop_panel.php';
        }

        if(isset($attr) && is_array($attr)){
            extract($attr, EXTR_OVERWRITE);
        }
        $this->view_path = LIW_WEB . 'views/' . $controller . '/' . $view . '.php';
        if(is_file($this->view_path)){
            require $this->view_path;
        }else{
            throw new \Exception('File: ' . $this->view_path . ' not exist!');
        }
        $this->view =  ob_get_clean();
        require LIW_WEB . 'views/layouts/' . Liw::$config['def_layout'] . '.php'; //подключение layout
    }*/

}
