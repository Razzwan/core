<?php
/**
 * Created by Razzwan.
 * Date: 26.08.15
 * Time: 15:36
 */
namespace liw\core\access;

use liw\core\Liw;

class AccessDefault implements AccessInterface
{
    static public function getWays()
    {
        if(Liw::$isGuest){
            $file = LIW_WEB . 'config/ways/guest.php';
        } else {
            $file = LIW_WEB . 'config/ways/user.php';
        }

        if(is_file($file)){
            return require_once $file;
        } else {
            throw new \Exception("File: " . $file . " not exist.");
        }
    }
}
