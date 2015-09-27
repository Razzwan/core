<?php
namespace liw\core\model\connect;

interface ConnectInterface
{
    public function get($sql, $type_param = null, array $param = null);

    public function push($sql, $type_param = null, array $param = null);
}
