<?php
namespace liw\core\widgets;

use liw\core\web\Request;

class Menu
{
    static private $html = '
        <nav>
            <ul class="nav masthead-nav">
                <li class="active"><a href="/">Привет!</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle my-drop" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Статьи <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/articles">PHP</a></li>
                        <li><a href="/articles">Ubuntu</a></li>
                        <li><a href="/articles">Git</a></li>
                        <li><a href="/articles">Bootstrap css</a></li>
                        <li><a href="/articles">Composer и др.</a></li>
                        <!--<li role="separator" class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">В</a></li>-->
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="/lessons" class="dropdown-toggle my-drop" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Уроки <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/lessons?part=1">PHP. ч-I</a></li>
                        <li><a href="/lessons?part=2">PHP. ч-II. ООП</a></li>
                    </ul>
                </li>
                <li><a href="/contacts">Для связи</a></li>
            </ul>
        </nav>
                    ';

    /**
     * Генерирует разметку меню
     * @param $arr
     * @return string
     */
    static public function init($arr)
    {
        if(is_array($arr)){
            if(!empty($arr['items'])){
                $html = '<ul ' . self::getOptions($arr['options']) . '>';
                foreach($arr['items'] as $item){
                    $html .= self::getLi($item['url'], $item['label']);
                }
                return $html . "</ul>";
            }
        }
    }

    static private function getOptions($options)
    {
        $html = '';
        foreach($options as $option=>$value){
            $html .= "{$option} = '{$value}'";
        }
        return $html;
    }

    static private function getLi($url, $label, $items = null, $options = null){
        if ($items === null){

            return "<li " . self::isActive($url) . "><a href = '{$url}'>{$label}</a></li>";

        } else {

            return "<li class = 'dropdown'>
                        <a href = '{$url}'" . self::getOptions($options) . ">{$label}</a>" .
                        self::init([
                            'options' => ['class' => 'dropdown-menu'],
                            'items'   => $items
                        ])
                    . "</li>";

        }
    }

    static private function isActive($url)
    {
        if($url == Request::$url){
            return "class = 'active' ";
        }
        return;
    }



}