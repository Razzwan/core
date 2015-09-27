<?php
namespace liw\core\model\connect;

interface ConnectInterface
{
    public function query($sql, array $param = null);
}
