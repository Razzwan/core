<?php
namespace liw\core\widgets;

use liw\core\web\Request;

class Menu
{
    /**
     * Возвращает html-разметку меню
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

    /**
     * генерирует элемент разметки
     * @param $options array
     * @return string
     */
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

    /**
     * Генерирует тег li и его внутренности
     * @param $url
     * @param $label
     * @return string
     */
    static private function renderItem($url, $label)
    {
        return "<li class = '" . self::isActive($url) . "'><a href = '{$url}'>{$label}</a></li>";
    }

    /**
     * Генерирует li class = dropdown
     * @param $url
     * @param $label
     * @param $items
     * @param null $options
     * @return string
     */
    static private function renderItems($url, $label, $items, $options = null)
    {
        $items = self::init([
            'options' => ['class' => 'dropdown-menu'],
            'items'   => $items
        ]);
        return "<li class = 'dropdown " . self::isActive($url) . "'><a href = '{$url}'" . self::renderOptions($options) . ">{$label}</a>" . $items . "</li>";
    }

    /**
     * Проверяет ссылку на совпадение с текущим адресом
     * Нужно для подстветки активного элемента меню
     * @param $url
     * @return string
     */
    static private function isActive($url)
    {
        if($url == Request::$url){
            return "active";
        }
        return '';
    }

}