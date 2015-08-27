<?php
/**
 * Created by Razzwan.
 * Date: 26.08.15
 * Time: 15:41
 */

namespace liw\core\access;


interface AccessInterface
{
    /**
     * $arr = [
     *      '/way/first' => ['controller' => 'name', 'action' => 'name', [optional 'options' => ['variable'=>'regV', ... ] ]],
     *      ...
     *  ];
     * @return array - массив разрешенных путей в виде, указаном выше
     */
    static public function getWays();
}