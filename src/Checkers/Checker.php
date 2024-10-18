<?php

namespace FilippoToso\ResourcePermissions\Checkers;

use FilippoToso\ResourcePermissions\Checkers\Contracts\Checker as Contract;

/**

 */
class Checker implements Contract
{
    public static function __callStatic($name, $arguments)
    {
        $class = config('resource-permission.checker');
        return $class::$name(...$arguments);
    }
}
