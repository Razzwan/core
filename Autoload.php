<?php
namespace liw;

use liw\core\Liw;

class Autoload
{
    /**
     * @var string
     */
    static private $prefix_web = 'web\\';

    /**
     * @var string
     */
    static private $prefix_core = 'liw\\';

    /**
     * @var string
     */
    static private $path_core = '';

    static public function register()
    {
        self::$path_core = dirname(__DIR__) . DIRECTORY_SEPARATOR;

        spl_autoload_register("self::loader");
    }
    
    static private function loader($loadClass)
    {
        $length = strlen(self::$prefix_web);
        $prefix = substr($loadClass, 0, $length-1);
        if($prefix=='liw'){
            $relative_class = substr($loadClass, $length);
            $file = str_replace('\\', '/', self::$path_core . $relative_class. ".php");

            if (file_exists($file)) {
                if(defined('DEVELOP') && DEVELOP){
                    Liw::$dev['classes'][] = $loadClass;
                }
                require_once $file;
            }
        } elseif($prefix=='web'){
            $relative_class = substr($loadClass, $length);
            $file = str_replace('\\', '/', LIW_WEB . $relative_class. ".php");

            if (file_exists($file)) {
                if(defined('DEVELOP') && DEVELOP){
                    Liw::$dev['classes'][] = $loadClass;
                }
                require_once $file;
            }
        }
    }
}