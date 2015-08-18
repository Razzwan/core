<?php
namespace liw\core;

use liw\core\routers\SmartRouter;

class App
{
    /**
     * ������������� ������, �������� ���������� � �����, �����������, ����� � ���������, ��������� � url,
     * ���� ����������� ����������� ��� ���������� �� $_SESSION, ���� � url ���������� ���
     * @var array
     */
    private $lcaa = [
        'language'   => '',
        'controller' => '',
        'action'     => '',
        'attributes' => []
    ];


    /**
     * �������� �����
     */
    private function loadLanguage()
    {
        if(!empty($_SESSION['language'])){
            $file_path = LIW_WEB . '/config/languages/' . $_SESSION['language'] . '.php';
            if(file_exists($file_path)){
                Liw::$lang = require $file_path;
                return;
            }
        }
        if(isset($_SESSION['language'])) unset($_SESSION['language']);
        Liw::$lang = require LIW_WEB . '/config/languages/' . Liw::$config['def_lang'] . '.php';

    }

    /**
     * ������ ����������
     * @param $config
     */
    public function start($config){
        try {
            // �������� ���������� ������
            set_error_handler([$this, 'show_errors']);

            session_start();
            session_name('liw');
            // ��������� ������ � ���������� ����������
            Liw::$config = $config;
            $this->loadLanguage();

            if (isset($_SESSION['user']) && !empty($_SESSION['user']['login'])){
                Liw::$user['login'] = true;
            }

            /**
             * �������� ������������� ������ � ������, ������������, ������ � ���������� �� �������
             */
            //$this->lcaa = Router::arr_FromUrl(); //������� ������
            if(SmartRouter::getRoute()){
                $this->lcaa = array_merge($this->lcaa, SmartRouter::getRoute());
            } else {
                throw new \Exception("No route: " . $_SERVER['REQUEST_URI']);
            }

            //���������� ����� � ������
            if(!empty($this->lcaa['language'])){
                $file_path = LIW_WEB . '/config/languages/' . $this->lcaa['language'] . '.php';
                if(file_exists($file_path)){
                    $_SESSION['language'] = $this->lcaa['language'];
                    Liw::$lang = require $file_path;
                }
            }

            $this->mvc($this->lcaa['controller'], $this->lcaa['action'], $this->lcaa['attributes']);
        }
        catch (\Exception $e) {
            $this->show_errors($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array $attributes
     * @throws \Exception
     */
    private function mvc ($controller, $action, $attributes = null) {
        $controller_route = '\web\controllers\\' . ucfirst($controller) . 'Controller';
        if (!class_exists($controller_route)) {
            throw new \Exception(Liw::$lang['message']['no_controller'] . $this->lcaa['controller']);
        }
        $controller_obj = new $controller_route();
        $this->lcaa['action'] = $action ?: $controller_obj->default_action;
        if (!method_exists($controller_obj, $this->lcaa['action'] . 'Action')) {
            throw new \Exception(Liw::$lang['message']['no_action'] . $this->lcaa['action']);
        }

        /**
         * ���� ���������� �����-���������� ����������, �� ������� ��� ������ �
         * �������� �����-��������(���), ��������� � ���� ��������
         *
         * ���� ���������� �������� �� ����������, �� ����� ������� �������� �� ��������,
         * �������� � �����������, ���� � ����������� �� ������ �������� ��-���������,
         * �� ����� ��������� �������� index, ���� ��� �����������, �� ����� ������
         */
        call_user_func_array([$controller_obj, $this->lcaa['action'] . 'Action'], $attributes);
    }

    /**
     * ����� ������� ������� ������
     * @param $errno integer
     * @param $errstr string
     * @param $file string
     * @param $line integer
     */
    public function show_errors($errno, $errstr, $file, $line)
    {
        $message = 'Error level: ' . $errno . '<hr>' . $errstr . '<hr>' . $file . '<hr>string: ' . $line . '<hr>';
        $view = View::getView();
        if (!defined('DEVELOP') || !DEVELOP){
            $view->render(Liw::$config['def_route'], 'error', [
                'error' => Liw::$lang['message']['error']
            ]);
        } else {
            $view->render(Liw::$config['def_route'], 'error', [
                'error' => $message
            ]);
        }
    }
}