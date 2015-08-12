<?php
namespace liw\core\base\validation;

/**
 * Статичный класс используется для проверки пользовательского ввода
 * и подготовки переменных перед выводом
 *
 * Class Clean
 * @package liw\core\base\validation
 */
class Clean {
    /**
     * Очищает от нежелательных символов input
     *
     * @param $str
     * @return string
     */
    static public function input($str)
    {
        return $str = filter_var(trim($str), FILTER_SANITIZE_STRING);
    }

    /**
     * Очищает от запрещенных символов url и переводит в нижний регистр
     *
     * @param $url
     * @return string
     */
    static public function url($url)
    {
        return $url = strtolower(filter_var($url, FILTER_SANITIZE_URL));
    }

    /**
     * Логин должен быть длиннее 2х символов, состоять из букво, чисел, знаков - и _
     *
     * @param $login
     */
    static public function login($login)
    {
        return $login;
    }

    /**
     * Емаил должен быть валидным email - адресом
     *
     * @param $email
     */
    static public function email($email)
    {
        return $email;
    }

    /**
     * Пасс долен быть минимум 6 символов
     *
     * @param $password
     */
    static public function password($password)
    {
        return $password;
    }

    /**
     * Очищает массив пост от нежелательных символов
     *
     * @param $post_arr
     * @return array or null
     */
    static public function post($post_arr)
    {
        if(is_array($post_arr)){
            return array_map('self::input', $post_arr);
        }
        return null;
    }

    /**
     * Возвращается true для значений  - 1, true, yes, on
     * возвращается false для значений - 0, false, no, off
     * в осальных случаях возвращает   - null
     *
     * @param $var
     * @return bool
     */
    static public function bool($var)
    {
        return filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Число с плавающей точкой
     *
     * @param $var
     * @return float
     */
    static public function float($var)
    {
        return filter_var($var, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Натуральное число входящее в диапазон между $min_range и $max_range
     *
     * @param $var
     * @param $min_range
     * @param $max_range
     * @return integer
     */
    static public function int($var, $min_range, $max_range)
    {
        return filter_var($var, FILTER_VALIDATE_INT, $min_range, $max_range);
    }

}
