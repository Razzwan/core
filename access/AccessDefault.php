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
            return include LIW_WEB . 'config/ways/guest.php';
        } else {
            return include LIW_WEB . 'config/ways/login.php';
        }
    }
}