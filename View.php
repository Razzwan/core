<?php
namespace liw\core;
use liw\core\web\Request;

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
     * html разметка страницы
     * @var string
     */
    public $view;

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
     * @var string
     * Лэйаут для текущего вида
     */
    static private $layout;

    private $var = [];

    public function __set($var, $value)
    {
        $this->var[$var] = $value;
    }

    public function __get($var)
    {
        return $this->var[$var];
    }

    /**
     * Массив значений, переданый в вид
     * @var array
     */
    public $attr = [];

    /**
     * Устанавливает название (title) документа по умалчанию
     */
    private function setVariables()
    {
        $this->title = Liw::$config['site_name'];
        $this->language = Request::$lang;
    }

    /**
     * запрет клонирования
     */
    private function __clone(){}

    public function getView($layout = null)
    {
        if (null === self::$_view) {
            self::$_view = new self();
        }

        /**
         * определяем текущий вид (лэйаут)
         */
        if($layout === null){
            if(Liw::$config['def_layout'] !== null){
                self::$layout = LIW_WEB . 'views/layouts/' . Liw::$config['def_layout'] . '.php';
            } else {
                $this->view = Lang::uage('def_layout_not_exist');
                require_once LIW_CORE . 'core/error/view/index.php';
                exit;
            }

        } else {
            self::$layout = LIW_WEB . 'views/layouts/' . $layout . '.php';
        }

        return self::$_view;
    }

    /**
     * Метод рендерит страницу, которая находится по маршруту LIW_WEB . 'views/' . $folder . '/' . $view . '.php'
     * В вид передается массив $attr пропущенный через функцию extract, что делает в нем доступными переменные,
     * соответствующие ключам массива.
     *
     * @param $folder
     * @param $view
     * @param null $attr
     * @throws \Exception
     */
    public function render($folder, $view, $attr = null)
    {
        if(!empty(ob_get_contents())){
            ob_end_clean();
        }
        ob_start();
        $this->view_path = LIW_WEB . 'views' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $view . '.php';
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
                include __DIR__ . '/develop/view/develop_panel.php';
            }
            include $this->view_path;
        }else{
            throw new \Exception('File: ' . $this->view_path . ' not exist!');
        }

        if(Request::isAjax()){
            echo ob_get_clean();
            return;
        }
        $this->view =  ob_get_clean();
        $this->setVariables();
        /**
         * нужен особый вывод ошибок, т.к. ошибка в лэйауте будет выводиться дважды, как обычная ошибка, и как ошибка
         * внутри вывода ошибки.
         */
        require_once self::$layout; //подключение layout
    }

    /**
     * Рендерит произвольный блок
     * @param $view
     * @param null $attr
     * @throws \Exception
     */
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

    public function showError($error)
    {
        $this->view = $error;
        require_once LIW_CORE . 'core/error/view/index.php';
    }

    public function twig($folder, $view, $attr = [])
    {
        if($view == '') throw new \Exception('Переменная вида не определена.');
        /**
         * Подгружаем папку с шаблонами
         */
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(LIW_WEB . 'views/' . $folder);
        $loader->addPath(LIW_WEB . "views/layouts", 'layouts');
        if (defined("DEVELOP") && DEVELOP === true){
            $debug = true;
        } else {
            $debug = false;
        }
        $twig = new \Twig_Environment($loader, array(
            'debug' => $debug));

        $twig->addExtension(new \Twig_Extension_Debug());

        if(!mb_ereg_match("^[\w\.\-]+\.twig$", $view)){
            $view = $view . '.twig';
        }
        echo $twig->render($view, $attr);
    }

}
