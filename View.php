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
     * Устанавливает название (title) документа по умалчанию
     */
    private function __construct()
    {
        $this->title = Liw::$config['site_name'];
    }

    /**
     * запрет клонирования
     */
    private function __clone(){}

    public static function getView($layout = null)
    {
        if (null === self::$_view) {
            self::$_view = new self();
            /**
             * определяем текущий вид (лэйаут)
             */
            if($layout === null){
                self::$layout = Liw::$config['def_layout'];
            } else {
                self::$layout = $layout;
            }
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
        /**
         * нужен особый вывод ошибок, т.к. ошибка в лэйауте будет выводиться дважды, как обычная ошибка, и как ошибка
         * внутри вывода ошибки.
         */
        require LIW_WEB . 'views/layouts/' . self::$layout. '.php'; //подключение layout
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

    public function twig($folder, $view)
    {
        /**
         * Подгружаем папку с шаблонами
         */
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(LIW_WEB . 'views/' . $folder);
        $loader->addPath(LIW_WEB . "views/layouts", 'layouts');
        $twig = new \Twig_Environment($loader, array(
            'debug' => true));

        $twig->addExtension(new \Twig_Extension_Debug());

        return $twig->loadTemplate($view . '.html.twig');
    }

}
