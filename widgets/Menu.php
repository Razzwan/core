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

    static private $widgetSettings;

    /**
     * @param $widgetSettings
     * @return string
     */
    static public function init($widgetSettings)
    {
        if(is_array($widgetSettings)){
            if($widgetSettings['items'] !== null){
                $html = '<ul ' . self::renderOptions($widgetSettings['options']) . '>';
                foreach($widgetSettings['items'] as $item){
                    if(!isset($item['items'])){
                        $html .= self::renderItem($item['url'], $item['label']);
                    } else {
                        $html .= self::renderItems($item['url'], $item['label'], $item['items'], $item['options']);
                    }

                }
                return $html . "</ul>";
            }
        }
        return '';
    }

    static private function renderOptions($options = null)
    {
        if($options === null){
            return '';
        }
        $html = '';
        foreach($options as $option => $value){
            $html .= "{$option} = '{$value}' ";
        }
        return $html;
    }

    static private function renderItem($url, $label)
    {
        return "<li class = '" . self::isActive($url) . "'><a href = '{$url}'>{$label}</a></li>";
    }

    static private function renderItems($url, $label, $items, $options = null)
    {
        $items = self::init([
            'options' => ['class' => 'dropdown-menu'],
            'items'   => $items
        ]);
        return "<li class = 'dropdown " . self::isActive($url) . "'><a href = '{$url}'" . self::renderOptions($options) . ">{$label}</a>" . $items . "</li>";
    }

    static private function isActive($url)
    {
        if($url == Request::$url){
            return "active";
        }
        return '';
    }



}